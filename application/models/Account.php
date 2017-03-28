<?php
class Account extends CI_Model {
	protected $CI;

	function __construct() {
		parent::__construct();
		$this->CI =& get_instance();
		//$this->load->model("Utility");
	}

	/**
	* Add user's details
	* return account id
	*/
	public function addAccount($accountData){
		$db = $this->CI->db;
		$db->trans_begin();
		//Account Data Insert
		foreach($accountData as $key => $val){
			$db->set( $val['key'], $val['value'], $val['escape'] );	
		}
		$db->insert("accounts");
		$account_id = $db->insert_id();

		if ($db->trans_status() === FALSE){
		    $db->trans_rollback();
		    return false;
		}else{
		    $db->trans_commit();
		    return $account_id;
		}
	}

	public function emailExists($email){
		$this->CI->db->select( "aemail" )->from( 'accounts' )->where( array( "aemail" => $email, "asocial" => "E" ) );
		$query = $this->CI->db->get();
		$row = $query->row();
		//echo $this->CI->db->last_query(); die();
		if ( $row ) return $row->aemail;
	}

	public function getAccount($account_id){
		$base_url = 'http://stopalldistractions.com/sad/';
		$this->CI->db->select( "id, aemail, afirstname, alastname, aprofile_photo" )->from('accounts')->where( "id", $account_id );
		$query = $this->CI->db->get();
		$row = $query->row();
		if($row->aprofile_photo != ''){
			$row->aprofile_photo = $base_url.'uploads/'.$row->aprofile_photo;
		}
		return $row;
	}

	public function getAccountField($account_id,$field){
		$base_url = base_url();
		$this->CI->db->select( $field )->from('accounts')->where( "id", $account_id );
		$query = $this->CI->db->get();
		$row = $query->row();
		return $row;
	}

	public function getAccountAuthInformation($account_id){
		$this->CI->db->select( "id, aemail, afirstname, alastname, apassword, asocial" )->from('accounts')->where( "id", $account_id );
		$query = $this->CI->db->get();
		$row = $query->row();
		return $row;
	}

	public function accountExist($social_id){
		$this->CI->db->select( "id" )->from('accounts')->where( "asocial_id", $social_id );
		$query = $this->CI->db->get();
		$row = $query->row();
		//echo $this->CI->db->last_query(); die();
		//if ( $row ) return $row->id;
		if(isset($row->id) && $row->id > 0)
			return $row->id;
		return false;
	}

	public function accountLogin($aemail,$apassword){
		$this->CI->db->select( "id, aemail, afirstname, alastname" )->from('accounts')->where( array( "aemail" => $aemail, "apassword" => md5($apassword) ) );
		$query = $this->CI->db->get();
		$row = $query->row();
		//echo $this->CI->db->last_query(); die();
		//if ( $row ) return $row->id;
		if(isset($row->id) && $row->id > 0)
			return $row;
		return false;
	}

	public function updateAccount($data,$accid){
		foreach ($data as $key => $value) {
			$data[$key] = $value;
		}
		$db = $this->CI->db;
		$db->update("accounts", $data, array( "id" => $accid ));
		//echo $db->last_query(); die;
		//return $db->affected_rows();
		return true;
	}

	public function updatePassword($data,$email){
		foreach ($data as $key => $value) {
			$data[$key] = $value;
		}
		$db = $this->CI->db;
		$db->update("accounts", $data, array( "aemail" => $email ));
		//echo $db->last_query(); die;
		//return $db->affected_rows();
		return true;
	}

}