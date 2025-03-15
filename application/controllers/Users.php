<?php
defined('BASEPATH') OR exit ('No direct script access allowed');

class Users extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database(); // Database load karein
        $this->load->helper('url'); // URL helper load karein
        $this->load->library('upload'); // File upload library load karein
    }

    
    // List all users
    public function index() {
        $query = $this->db->get('users'); // 'users' table se data fetch karein
        $data['users'] = $query->result(); // Data ko $data array mein store karein
        $this->load->view('users/list', $data); // View load karein aur data pass karein
    }

    // Show form to add a new user
    public function add() {
        $this->load->view('users/add'); // Add user form ka view load karein
    }

    // Save new user
    public function save() {
        // Form validation rules
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
    
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
                'profile_picture' => $profile_picture
            );
    
            // Data ko database mein insert karein
            $this->db->insert('users', $data);
    
            // Users list page par redirect karein
            redirect('users');
        }
    }

    // Show form to edit a user
    public function edit($id) {
        $query = $this->db->get_where('users', array('id' => $id)); // Specific user ka data fetch karein
        // select * from user where id = $id;
        $data['user'] = $query->row(); // Data ko $data array mein store karein
        $this->load->view('users/edit', $data); // Edit user form ka view load karein
    }

    public function update($id) {
        // Form validation rules
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
    
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
    
            $this->db->where('id', $id);
            $this->db->update('users', $data);
    
            $this->session->set_flashdata('success', 'User updated successfully.');
            redirect('users');
        }
    }
    
    // Delete user
    public function delete($id) {
        // User ki profile picture ka path fetch karein
        $user = $this->db->get_where('users', array('id' => $id))->row();
        $profile_picture_path = $user->profile_picture;

        // Agar profile picture exist karti hai, toh use delete karein
        if ($profile_picture_path && file_exists($profile_picture_path)) {
            unlink($profile_picture_path); // File delete karein
        }

        // User ko database se delete karein
        $this->db->where('id', $id);
        $this->db->delete('users');

        // Users list page par redirect karein
        redirect('users');
    }
}