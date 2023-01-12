<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		$inner_data = [
			'error' => false,
			'error_msg' => null
		];
		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$email = $this->input->post('email');
			$error = false;
			$error_msg = null;

			if (!$inner_data['error'] && empty($email)) {
				$inner_data['error'] = true;
				$inner_data['error_msg'] = 'Email is empty.';
			}

			if (!$inner_data['error'] && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$inner_data['error'] = true;
				$inner_data['error_msg'] = 'Email is not valid.';
			}

			if (!$inner_data['error']) {
				$session = $email . time() . rand(1000, 9999);
				$hash_session = md5($session);
				$this->session->set_userdata('email', $email);
				$this->session->set_userdata('user_hash', $hash_session);

				$finil_data = [
					'email' => $email,
					'hash_session' => $hash_session
				];
				$this->db->insert('email', $finil_data);
				return redirect('form');
			} else {
				$inner_data['error'] = $error;
				$inner_data['error_msg'] = $error_msg;
			}
		}

		$data['content'] = $this->load->view('home', $inner_data, true);
		$this->load->view('master_page', $data);
	}

	public function form()
	{
		$email = $this->session->userdata('email');
		$user_hash = $this->session->userdata('user_hash');
		if (empty($email) || empty($user_hash)) {
			$query = $this->db->get_where('email', ['email' => $email, 'hash_session' => $user_hash]);
			if ($query->num_rows() == 0) {
				show_404();
				exit();
			}
		}


		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$user_data = $this->input->post();
			$query = $this->db->get_where('email', ['email' => $email, 'hash_session' => $user_hash]);
			$user_data['user_id'] = $query->row()->id;

			$this->db->insert('user_data', $user_data);
			$prompt = 'I remember ' . $user_data['input_one'] . ' in my ' . $user_data['input_two'] . ' ' . $user_data['input_three'] . ' ' . $user_data['input_four'] . ' and feeling a bit ' . $user_data['input_five'] . ' but mostly ' . $user_data['input_six'] . ' in ' . $user_data['input_seven'];
			//$prompt = 'Exploring how people and their shoes could form memories of a city.” Graphic novel illustrated by Kishimoto published on Shonen Jump 2017  black and white pen and ink highly detailed.';
			$prediction_id = $this->api_predictions($prompt);

			$api_status = 'starting';
			while (in_array($api_status, ['processing', 'starting', 'failed', 'canceled'])) {
				$res = $this->api_predictions_id($prediction_id);
				$api_status = $res['status'];
			}

			// Image
			$image_name = $user_hash.'.png';
			$image_path = 'assets/img/result/'.$image_name;

			$arrContextOptions=array(
				"ssl"=>array(
					"verify_peer"=>false,
					"verify_peer_name"=>false,
				),
			);  
			
			$response = file_get_contents($res['output'][0], false, stream_context_create($arrContextOptions));
			
			file_put_contents($image_path, $response);

			$final_data = [
				'user_id' => $user_data['user_id'],
				'email' => $email,
				'result_json' => json_encode($res),
				'image' => $image_name
			];

			$this->db->insert('result', $final_data);

			return redirect('result');

			// API section
		} else {
			$inner_data = [];
			$data['content'] = $this->load->view('form', $inner_data, true);
			$this->load->view('master_page', $data);
		}
	}


	public function result()
	{
		$email = $this->session->userdata('email');
		$user_hash = $this->session->userdata('user_hash');
		$user_id = 0;
		$inner_data = [];

		if (!empty($email) && !empty($user_hash)) {
			$query = $this->db->get_where('email', ['email' => $email, 'hash_session' => $user_hash]);
			if ($query->num_rows() == 0) {
				show_404();
				exit();
			} else {
				$user_id = $query->row()->id;
			}
		} else {
			show_404();
			exit();
		}

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$again = $this->input->post('again');
			if ($again == 'again') {
				$session = $email . time() . rand(1000, 9999);
				$hash_session = md5($session);
				$this->session->set_userdata('email', $email);
				$this->session->set_userdata('user_hash', $hash_session);

				$finil_data = [
					'email' => $email,
					'hash_session' => $hash_session
				];
				$this->db->insert('email', $finil_data);
				return redirect('form');
			} else {
				return redirect('home');
			}
		}

		$user_input = $this->db->get_where('user_data', ['user_id' => $user_id]);
		if ($user_input->num_rows() > 0) {
			$inner_data['user_input'] = $user_input;
		}

		$result = $this->db->get_where('result', ['user_id' => $user_id]);
		$inner_data['image'] = $result->row()->image;

		$data['content'] = $this->load->view('result', $inner_data, true);
		$this->load->view('master_page', $data);
	}


	private function api_predictions($prompt)
	{
		$ch = curl_init('https://api.replicate.com/v1/predictions');
		$post = json_encode([
			"version" => "f178fa7a1ae43a9a9af01b833b9d2ecf97b1bcb0acfd2dc5dd04895e042863f1",
			"input" => ["prompt" => $prompt]
		], true);
		$authorization = "Authorization: Token 68d56162915aeed3d131dd1655126d19d2ca6f0b";
		$content_type = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($content_type, $authorization));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($result);
		return $result->id;
	}

	private function api_predictions_id($id)
	{
		$ch = curl_init();
		$url = 'https://api.replicate.com/v1/predictions/' . $id;

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');


		$headers = array();
		$headers[] = 'Authorization: Token 68d56162915aeed3d131dd1655126d19d2ca6f0b';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);
		$res = json_decode($result, true);
		return $res;
	}
}
