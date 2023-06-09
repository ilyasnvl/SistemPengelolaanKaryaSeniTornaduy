<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Publishers_model extends CI_Model
{

    public $table = 'publishers';
    public $id = 'id';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
    }

    // get all
    function get_all()
    {
        $this->db->order_by($this->id, $this->order);
        return $this->db->get($this->table)->result();
    }

    function getPublist()
    {
        $this->db->order_by('name','ASC');
        return $this->db->get('publishers')->result();
    }

    // get data by id (Controller - ManageEvent, Publisher)
    function get_by_id($id)
    {
        return $this->db->get_where('publishers',['id' => $id])->row();
    }

    // get data by id (Controller - Auth)
    function getByLoginId($id_akun)
    {
        $this->db->select('*')
                 ->from('publishers p')
                 ->join('login l', 'l.login_id = p.pub_login')
                 ->where('p.pub_login', $id_akun);
        return $this->db->get()->row();
    }
    
    // check id 
    function isExistId($id) {
        $this->db->where($this->id, $id);
        $query = $this->db->get($this->table);

        if($query->num_rows() > 0) {
            return true;
        }
        return false;
    }

    // insert data
    function insert($data)
    {
        $this->db->insert($this->table, $data);
    }

    // update data 
    function update($id, $data)
    {
        $this->db->where($this->id, $id);
        $this->db->update($this->table, $data);
    }

    // (Controller - Publisher)
    function changePassword($id, $data)
    {
        $this->db->where('login_id', $id);
        $this->db->update('login', $data);
    }

    // delete data
    function delete($id)
    {
        $this->db->where($this->id, $id);
        $this->db->delete($this->table);
    }

    // menampilkan semua data event berdasarkan publisher (Controller - Home)
    function get_all_event($pub_id)
    {
        $this->db->select('e.event_id, e.event_name, e.event_image, e.event_type, e.event_slug,
                          e.start_date, e.event_status, e.event_pub, e.event_created')
                 ->from('event e')
                 ->join('publishers p','e.event_pub = p.pub_id')
                 ->where('e.event_pub', $pub_id)
                 ->order_by('e.event_created', $this->order);
        return $this->db->get()->result();
    }

    // ambil data bank (Controller - Publisher)
    function get_bank_data()
    {
        $this->db->order_by('bank_name', 'ASC');
        return $this->db->get('bank')->result();
    }

    public function getTopPublisher()
    {
        $this->db->select('p.*, COUNT(e.publisher) as publishers')
                 ->from('publishers p')
                 ->join('events e','e.publisher = p.id')
                 ->where('e.status','approved')
                 ->group_by('p.id')
                 ->order_by('publishers','desc')
                 ->limit(4);
        return $this->db->get()->result_array();
    }

}

/* End of file Publishers_model.php */
/* Location: ./application/models/Publishers_model.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2019-02-27 03:56:43 */
/* http://harviacode.com */