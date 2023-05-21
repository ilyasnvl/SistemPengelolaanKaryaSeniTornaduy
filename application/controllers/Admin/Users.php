<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $models = array(
            'Admin/Users_model' => 'mUser',
            'Admin/Ticket_model' => 'mTiket',
            'Admin/Event_model' => 'mEvent',
            'Admin/Publishers_model' => 'mPub',
        );
        $this->load->model(array($models));
        $this->load->library('form_validation');
    }

    function index()
    {
        $users = $this->mUser->getAll();

        $data = array(
            'title' => 'Users',
            'users_data' => $users,
        );
        $this->load->view('_admin/users/v_user',$data);
    }
    
    function publisher()
    {
        $publishers = $this->db->order_by('created_at','desc')->get('publishers')->result();

        $data = array(
            'title' => 'Users',
            'publishers' => $publishers,
        );

        $this->load->view('_admin/publishers/v_publisher',$data);
    }

    function read($id)
    {
        $row = $this->mUser->getById($id);

        if ($row) {
            $data = array(
                'title'        => 'Users',
                'user_id'      => $row->id,
                'user_name'    => $row->name,
                'user_tel'     => $row->phone_number,
                'user_bio'     => $row->short_bio,
                'user_address' => $row->address,
                'user_image'   => $row->profile_picture,
                'user_email'   => $row->email,
                'joindate'     => $row->created_at,
                'tiket_data'   => $this->mTiket->ticketByUser($id),
                'publisher'  => $this->db->get_where('publishers',['id' => $id])->row(),
                'events_data'  => $this->mEvent->event_publisher($id),
            );
            $this->load->view('_admin/users/v_userDetail',$data);
        } else {
            echo '<script>hostory.go(-1)</script>';
        }
    }

    function update($id,$error = null) 
    {
        $row = $this->mUser->getById($id);

        if ($row) {
            $data = array(
                'button'          => 'Edit',
                'action'          => site_url('admin/peserta/aksi-edit'),
                'title'           => 'Users',
                'user_id'         => set_value('user_id', $row->id),
                'user_name'       => set_value('user_name', $row->name),
                'user_tel'        => set_value('user_tel', $row->phone_number),
                'user_bio'        => set_value('user_bio', $row->short_bio),
                'user_address'    => set_value('user_address', $row->address),
                'user_image'      => set_value('user_image', $row->profile_picture),
                'user_image_new'  => set_value('user_image_new'),
                'email'           => set_value('email', $row->email),
                'password'        => set_value('password', $row->password),
                'error'           => $error,
            );
            $this->load->view('_admin/users/v_userForm',$data);
        } else {
            echo "<script>history.go(-1)</script>";
        }
    }

    function update_action()
    {
        $row = $this->mUser->get_by_id($this->input->post('user_id',TRUE));

        if (trim($this->input->post('email',TRUE)) == $row->email) {
            $this->form_validation->set_rules('email', 'Email', 'trim');   
        } else {
            $this->form_validation->set_rules('email', 'Email', 'is_unique[users.email]',
                    array('is_unique' => '%s sudah digunakan'));   
        }

        if (trim($this->input->post('user_tel',TRUE)) == $row->phone_number) {
            $this->form_validation->set_rules('user_tel', 'Nomor Hp', 'trim|max_length[13]',
            array('max_length' => '%s maksimal 13 digit'));
        } else {
            $this->form_validation->set_rules('user_tel', 'Nomor Hp', 'is_unique[users.phone_number]|max_length[13]',
                    array('is_unique' => '%s sudah digunakan',
                          'max_length' => '%s maksimal 13 digit'
                    ));
        }
        
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
        
        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('user_id', TRUE));
        } else {
            $id      = $this->input->post('user_id',TRUE);
            $idLogin = $this->input->post('user_login',TRUE);
            $name    = trim($this->input->post('user_name',TRUE));
            $tel     = trim($this->input->post('user_tel',TRUE));
            $bio     = trim($this->input->post('user_bio',TRUE));
            $alamat  = trim($this->input->post('user_address',TRUE));
            $email   = trim($this->input->post('email',TRUE));
            $password   = $this->input->post('password',TRUE);
            $image_user = $this->input->post('user_image',TRUE);
            $nameFileBaru = $id.'_'.time();

            // File Gambar
            $config['upload_path']      = './assets/images/images-user/';
            $config['allowed_types']    = 'jpg|png';
            $config['overwrite']		= true;
            $config['max_size']         = 500;
            $config['file_name']        = $nameFileBaru;
            
            $this->load->library('upload', $config);

            if ($this->upload->do_upload('user_image_new')) {
                $dataImage = array('upload_data' => $this->upload->data());
                $userImageNew = $dataImage['upload_data']['file_name'];
                $dataUser = array(
                    'id' => $id,
                    'name' => $name,
                    'email' => $email,
                    'phone_number' => $tel,
                    'password' => sha1($password),
                    'short_bio' => $bio,
                    'address' => $alamat,
                    'profile_picture' => $userImageNew,
                );
                $this->mUser->update($id, $dataUser);
                unlink('./assets/images/images-user/'.$image_user);
                $this->session->set_flashdata('success_update', 'Profil berhasil diganti');
                redirect(site_url('profil-saya'));
            } elseif (empty($_FILES['user_image_new']['name'])) {
                $dataUser = array(
                    'id' => $id,
                    'name' => $name,
                    'email' => $email,
                    'phone_number' => $tel,
                    'password' => sha1($password),
                    'short_bio' => $bio,
                    'address' => $alamat,
                    'profile_picture' => $image_user,
                );
                $this->mUser->update($id, $dataUser);
                $this->session->set_flashdata('success_update', 'Profil berhasil diganti');
                redirect(site_url('admin/users/'));
            } else {
                $this->profile($this->upload->display_errors());
                $userImageNew = null;
            }
        }
    }

    // blokir dan buka blokir
    function block($id)
    {
        // $status = 0;
        // $this->mUser->update($id,$status);
        // redirect('admin/peserta');
        $row = $this->mUser->getById($id);
        if ($row) {
            $data['status'] = 0;
            $this->mUser->update($id,$data);
            
            redirect('admin/peserta');
        } else {
            echo "<script>history.go(-1)</script>";
        }
    }

    function block_publisher($id)
    {
        // $status = 0;
        // $this->mUser->update($id,$status);
        // redirect('admin/peserta');
        $row = $this->mPub->getById($id);
        if ($row) {
            $data['status'] = 'submitted';
            $this->mPub->update($id,$data);
            
            redirect('admin/publisher');
        } else {
            echo "<script>history.go(-1)</script>";
        }
    }

    function unBlock_publisher($id)
    {
        // $status = 0;
        // $this->mUser->update($id,$status);
        // redirect('admin/peserta');
        $row = $this->mPub->getById($id);
        if ($row) {
            $data['status'] = 'approved';
            $this->mPub->update($id,$data);
            
            redirect('admin/publisher');
        } else {
            echo "<script>history.go(-1)</script>";
        }
    }

    public function delete($id)
    {
        $row = $this->mUser->getById($id);

        if ($row) {
            $this->mUser->delete($id);
            $this->session->set_flashdata("email_send", "<div class='alert alert-success alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>Pesan berhasil dihapus</div>");
            redirect('admin/peserta');
        } else {
            echo "<script>history.go(-1)</script>";
        }
    }

    public function hapus($id)
    {
        $row = $this->mPub->getById($id);

        if ($row) {
            $this->mPub->delete($id);
            $this->session->set_flashdata("email_send", "<div class='alert alert-success alert-dismissible'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>Pesan berhasil dihapus</div>");
            redirect('admin/publisher');
        } else {
            echo "<script>history.go(-1)</script>";
        }
    }

    function unBlock($id)
    {
        // $status = 1;
        // $this->mUser->update($id,$status);
        // redirect('admin/peserta');
        $row = $this->mUser->getById($id);
        if ($row) {
            $data['status'] = 1;
            $this->mUser->update($id,$data);
            
            redirect('admin/peserta');
        } else {
            echo "<script>history.go(-1)</script>";
        }
    }

    // approve and reject as publisher
    function approve($id)
    {
        $data['status'] = 'approved';
        $data['status_description'] = 'OK';
        $this->db->where('id',$id)->update('publishers',$data);
        redirect('admin/peserta/'.$id);
    }
    function reject($id)
    {
        $data['status'] = 'rejected';
        $data['status_description'] = $this->input->post('status_description');
        $this->db->where('id',$id)->update('publishers',$data);
        redirect('admin/peserta/'.$id);
    }
}
