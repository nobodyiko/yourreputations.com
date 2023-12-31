<?php

namespace GT3\ThemesCore\Registration;

use GT3\ThemesCore\Logs;
use WP_Error;
use WP_REST_Server;
use WP_REST_Request;

trait Rest_Trait {

	public function verify_permission(WP_REST_Request $request){
		$nonce = $request->get_param('_nonce');

		return wp_verify_nonce($nonce, $this->_nonce_key) && current_user_can('administrator');
	}

	public function rest_init(){
		$namespace = 'gt3_core/v1/registration';

		register_rest_route(
			$namespace,
			'check_code',
			array(
				array(
					'methods'             => WP_REST_Server::ALLMETHODS,
					'permission_callback' => array( $this, 'verify_permission' ),
					'callback'            => array( $this, 'rest_check_code' ),
				)
			)
		);

		register_rest_route(
			$namespace,
			'deactivate',
			array(
				array(
					'methods'             => WP_REST_Server::ALLMETHODS,
					'permission_callback' => array( $this, 'verify_permission' ),
					'callback'            => array( $this, 'rest_deactivate' ),
				)
			)
		);

		register_rest_route(
			$namespace,
			'activate',
			array(
				array(
					'methods'             => WP_REST_Server::ALLMETHODS,
					'permission_callback' => array( $this, 'verify_permission' ),
					'callback'            => array( $this, 'rest_activate' ),
				)
			)
		);

		register_rest_route(
			$namespace,
			'register_user',
			array(
				array(
					'methods'             => WP_REST_Server::ALLMETHODS,
					'permission_callback' => array( $this, 'verify_permission' ),
					'callback'            => array( $this, 'rest_register_user' ),
				)
			)
		);

		register_rest_route(
			$namespace,
			'reset',
			array(
				array(
					'methods'             => WP_REST_Server::ALLMETHODS,
					'permission_callback' => array( $this, 'verify_permission' ),
					'callback'            => array( $this, 'rest_reset' ),
				)
			)
		);

		register_rest_route(
			$namespace,
			'set-update',
			array(
				array(
					'methods'             => WP_REST_Server::ALLMETHODS,
					'permission_callback' => array( $this, 'verify_permission' ),
					'callback'            => array( $this, 'rest_set_update' ),
				)
			)
		);

	}

	public function rest_check_code(\WP_REST_Request $request){
		$code = $request->get_param('code');

		$data = $this->fetch_check_user($code, true);

		if(is_wp_error($data)) {
			/** @var WP_Error $data */
			$data = array(
				'error'   => true,
				'respond' => $data->get_error_message()
			);
		}

		$actions = array(
			'purchase_code'  => '',
			'support_time'   => '',
			'activated'      => false,
			'already_linked' => false,
		);

		if(key_exists('actions', $data) && is_array($data['actions'])) {
			$actions = $data['actions'];

			$actions = array_merge($actions, $data['actions']);
			if($actions['already_linked']) {
				$this->save($data, true);
			}

			Logs::instance()->save_log(array(
				'type' => E_USER_WARNING,
				'message' => 'User Activated. ',
				'file' => 'rest_actions',
				'line' => __LINE__,
			));
		}


		$actions['support_time'] = $this->get_support_time_left();

		return rest_ensure_response(
			array_merge(
				array(
					'error' => false,
					'data'  => $actions
				), (array)$data
			)
		);
	}

	public function rest_deactivate(\WP_REST_Request $request){

		$data = $this->deactivate_code();

		if(is_wp_error($data)) {
			/** @var WP_Error $data */
			$data = array(
				'error'   => true,
				'respond' => $data->get_error_message()
			);
		}

		$data['actions'] = array(
			'activated'      => false,
			'purchase_code'  => '',
			'support_time'   => '',
			'already_linked' => false,
		);

		Logs::instance()->save_log(array(
			'type' => E_USER_WARNING,
			'message' => 'User Deactivated. ',
			'file' => 'rest_actions',
			'line' => __LINE__,
		));
		$this->save($data, true);

		return rest_ensure_response(
			array_merge(
				array(
					'data'    => $this->get_settings(),
					'success' => true,
					'respond' => 'Product is deactivated!',
				)
			)
		);
	}

	public function rest_activate(\WP_REST_Request $request){

		$data = $this->activate_code();

		if(is_wp_error($data)) {
			/** @var WP_Error $data */
			$data = array(
				'error'   => true,
				'respond' => $data->get_error_message()
			);
		}

		Logs::instance()->save_log(array(
			'type' => E_USER_WARNING,
			'message' => 'User Activated. ',
			'file' => 'rest_actions',
			'line' => __LINE__,
		));

		$this->save($data, true);

		return rest_ensure_response(
			array_merge(
				array(
					'data'    => $this->get_settings(),
					'success' => true,
					'respond' => 'Product is activated!'
				)
			)
		);
	}

	public function rest_register_user(\WP_REST_Request $request){

		$email         = $request->get_param('email');
		$purchase_code = $request->get_param('purchase_code');

		$data = $this->register_user(
			array(
				'email'         => $email,
				'purchase_code' => $purchase_code,
			)
		);

		if(is_wp_error($data)) {
			/** @var WP_Error $data */
			$data = array(
				'error'   => true,
				'respond' => $data->get_error_message()
			);
		}

		$actions = array(
			'purchase_code'  => '',
			'support_time'   => '',
			'activated'      => false,
			'already_linked' => false,
		);

		if(key_exists('actions', $data) && is_array($data['actions'])) {
			$actions = $data['actions'];

			$actions = array_merge($actions, $data['actions']);
			if($actions['already_linked']) {
				$this->save($data, true);
			}
		}

		return rest_ensure_response(
			array_merge(
				array(
					'error' => false,
					'data'  => $this->get_settings()
				), (array)$data
			)
		);
	}

	public function rest_reset(\WP_REST_Request $request){

		$this->save(
			array(
				'activated'      => false,
				'purchase_code'  => '',
				'support_time'   => '',
				'already_linked' => false,
			)
		);

		Logs::instance()->save_log(array(
			'type' => E_USER_WARNING,
			'message' => 'User Reset. ',
			'file' => 'rest_actions',
			'line' => __LINE__,
		));

		return rest_ensure_response(
			array(
				'error' => false,
				'data'  => $this->get_settings()
			)
		);
	}
}
