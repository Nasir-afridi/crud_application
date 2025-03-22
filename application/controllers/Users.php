<?php
defined('BASEPATH') OR exit ('No direct script access allowed');

class Users extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session'); 
        $this->load->database(); 
        $this->load->helper('url'); 
        $this->load->library('upload'); 
    }

    
    // List all users
    public function index() {
        // Agar user logged in nahi hai, toh login page par redirect karein
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }
    
        // Session se user ka naam fetch karein
        $data['username'] = $this->session->userdata('username');
    
        // Users list fetch karein
        $query = $this->db->get('users'); // 'users' table se data fetch karein
        $data['users'] = $query->result(); // Data ko $data array mein store karein
    
        // View load karein aur data pass karein
        $this->load->view('users/list', $data);
    }

    public function login() {
        // Agar user already logged in hai, toh dashboard par redirect karein
        if ($this->session->userdata('user_id')) {
            redirect('users');
        }
        $this->load->view('users/login'); // Login view load karein
    }

    public function do_login() {
        // Form validation rules
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');
    
        if ($this->form_validation->run() == FALSE) {
            // Validation failed, show errors
            $this->load->view('users/login');
        } else {
            $email = $this->input->post('email');
            $password = $this->input->post('password');
    
            // Database se user ko fetch karein
            $user = $this->db->get_where('users', array('email' => $email))->row();
    
            // Password verify karein
            if ($user && password_verify($password, $user->password)) {
                // Session data set karein
                $session_data = array(
                    'user_id' => $user->id,
                    'username' => $user->name,
                    'email' => $user->email,
                    'logged_in' => TRUE
                );
                $this->session->set_userdata($session_data);
    
                // Dashboard par redirect karein
                redirect('users');
            } else {
                // Invalid credentials, error message dikhayen
                $this->session->set_flashdata('error', 'Invalid email or password');
                redirect('login');
            }
        }
    }

    public function logout() {
        // Session destroy karein
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('logged_in');
        $this->session->sess_destroy();
    
        // Login page par redirect karein
        redirect('login');
    }

    // Show form to add a new user
    public function add() {
        // Agar user logged in nahi hai, toh login page par redirect karein
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }
        $this->load->view('users/add'); // Add user form ka view load karein
    }
    // Save new user
    public function save() {
        // Form validation rules
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
    
        if ($this->form_validation->run() == FALSE) {
            // Validation failed, show errors
            $this->load->view('users/add');
        } else {
            // File upload configuration
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = 2048;
    
            $this->upload->initialize($config);
    
            if ($this->upload->do_upload('profile_picture')) {
                $file_data = $this->upload->data();
                $profile_picture = 'uploads/' . $file_data['file_name'];
            } else {
                $profile_picture = '';
            }
    
            // Form data ko array mein collect karein
            $data = array(
                'name' => $this->input->post('name'),
                'email' => $this->input->post('email'),
                'age' => $this->input->post('age'),
                'skills' => $this->input->post('skills'),
                'address' => $this->input->post('address'),
                'designation' => $this->input->post('designation'),
                'profile_picture' => $profile_picture,
                'password' => password_hash($this->input->post('password'), PASSWORD_BCRYPT) // Password hash karein
            );
    
            // Data ko database mein insert karein
            $this->db->insert('users', $data);
    
            // Users list page par redirect karein
            redirect('users');
        }
    }

    public function update($id) {
        // Form validation rules
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'min_length[6]');
    
        if ($this->form_validation->run() == FALSE) {
            // Validation failed, show errors
            $this->session->set_flashdata('error', validation_errors());
            redirect('users/edit/' . $id);
        } else {
            $email = $this->input->post('email');
    
            // **CURRENT USER KA EMAIL CHECK KARO**
            $currentUser = $this->db->get_where('users', array('id' => $id))->row();
    
            if ($currentUser->email !== $email) {
                // **AGAR EMAIL CHANGE HO RHA HAI, TO DUPLICATE CHECK KARO**
                $existingUser = $this->db->where('email', $email)->where('id !=', $id)->get('users')->row();
    
                if ($existingUser) {
                    // **AGAR EMAIL ALREADY MAUJOOD HAI, TO ERROR DIKHAYO**
                    $this->session->set_flashdata('error', 'The Email field must contain a unique value.');
                    redirect('users/edit/' . $id);
                }
            }
    
            // File upload configuration
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = 2048;
    
            $this->upload->initialize($config);
    
            if ($this->upload->do_upload('profile_picture')) {
                $file_data = $this->upload->data();
                $profile_picture = 'uploads/' . $file_data['file_name'];
    
                // Purani profile picture delete karein (agar exist karti hai)
                if ($currentUser->profile_picture && file_exists($currentUser->profile_picture)) {
                    unlink($currentUser->profile_picture);
                }
            } else {
                $profile_picture = $this->input->post('existing_profile_picture');
            }
    
            // **DATA UPDATE KARO**
            $data = array(
                'name' => $this->input->post('name'),
                'email' => $email,
                'age' => $this->input->post('age'),
                'skills' => $this->input->post('skills'),
                'address' => $this->input->post('address'),
                'designation' => $this->input->post('designation'),
                'profile_picture' => $profile_picture
            );
    
            // Password update karein (agar diya gaya hai)
            $new_password = $this->input->post('password');
            if (!empty($new_password)) {
                $data['password'] = password_hash($new_password, PASSWORD_BCRYPT);
            }
    
            $this->db->where('id', $id);
            $this->db->update('users', $data);
    
            $this->session->set_flashdata('success', 'User updated successfully.');
            redirect('users');
        }
    }

    public function edit($id) {
        // Agar user logged in nahi hai, toh login page par redirect karein
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }
        $query = $this->db->get_where('users', array('id' => $id)); // Specific user ka data fetch karein
        $data['user'] = $query->row(); // Data ko $data array mein store karein
        $this->load->view('users/edit', $data); // Edit user form ka view load karein
    }
    
    public function delete($id) {
        // Agar user logged in nahi hai, toh login page par redirect karein
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }

        // User ki profile picture ka path fetch karein
        $user = $this->db->get_where('users', array('id' => $id))->row();

        // Check karein ki user exist karta hai ya nahi
        if (!$user) {
            $this->session->set_flashdata('error', 'User not found');
            redirect('users');
        }

        $profile_picture_path = $user->profile_picture;

        // Agar profile picture exist karti hai, toh use delete karein
        if ($profile_picture_path && file_exists($profile_picture_path)) {
            if (is_writable($profile_picture_path)) {
                unlink($profile_picture_path); // File delete karein
            } else {
                $this->session->set_flashdata('error', 'Unable to delete profile picture: Permission denied');
            }
        }

        // User ko database se delete karein
        $this->db->where('id', $id);
        $this->db->delete('users');

        // Users list page par redirect karein
        $this->session->set_flashdata('success', 'User deleted successfully');
        redirect('users');
    }
}