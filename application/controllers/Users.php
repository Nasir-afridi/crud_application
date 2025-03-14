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
        // File upload configuration
        $config['upload_path'] = './uploads/'; // Folder where images will be uploaded
        $config['allowed_types'] = 'jpg|jpeg|png|gif'; // Allowed file types
        $config['max_size'] = 2048; // Max file size in KB (2MB)

        $this->upload->initialize($config);

        if ($this->upload->do_upload('profile_picture')) {
            // File uploaded successfully
            $file_data = $this->upload->data();
            $profile_picture = 'uploads/' . $file_data['file_name']; // File path to store in database
        } else {
            // File upload failed
            $profile_picture = ''; // Set default or handle error
        }

        // Form data ko array mein collect karein
        $data = array(
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'age' => $this->input->post('age'),
            'skills' => $this->input->post('skills'),
            'address' => $this->input->post('address'),
            'designation' => $this->input->post('designation'),
            'profile_picture' => $profile_picture // File path store karein
        );
        $this->db->insert('users', $data); // Data ko 'users' table mein insert karein
        redirect('users'); // Users list page par redirect karein
    }

    // Show form to edit a user
    public function edit($id) {
        $query = $this->db->get_where('users', array('id' => $id)); // Specific user ka data fetch karein
        // select * from user where id = $id;
        $data['user'] = $query->row(); // Data ko $data array mein store karein
        $this->load->view('users/edit', $data); // Edit user form ka view load karein
    }

    // Update user
    public function update($id) {
        // File upload configuration
        $config['upload_path'] = './uploads/'; // Folder where images will be uploaded
        $config['allowed_types'] = 'jpg|jpeg|png|gif'; // Allowed file types
        $config['max_size'] = 2048; // Max file size in KB (2MB)

        $this->upload->initialize($config);

        if ($this->upload->do_upload('profile_picture')) {
            // File uploaded successfully
            $file_data = $this->upload->data();
            $profile_picture = 'uploads/' . $file_data['file_name']; // File path to store in database

            // Purani profile picture delete karein (agar exist karti hai)
            $user = $this->db->get_where('users', array('id' => $id))->row();
            if ($user->profile_picture && file_exists($user->profile_picture)) {
                unlink($user->profile_picture); // Purani file delete karein
            }
        } else {
            // File upload failed, use existing image
            $profile_picture = $this->input->post('existing_profile_picture');
        }

        // Form data ko array mein collect karein
        $data = array(
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'age' => $this->input->post('age'),
            'skills' => $this->input->post('skills'),
            'address' => $this->input->post('address'),
            'designation' => $this->input->post('designation'),
            'profile_picture' => $profile_picture // File path store karein
        );
        $this->db->where('id', $id); // Specific user ko identify karein
        $this->db->update('users', $data); // Data ko update karein
        redirect('users'); // Users list page par redirect karein
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