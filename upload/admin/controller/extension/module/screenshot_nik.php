<?php
class ControllerExtensionModuleScreenshotNik extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/screenshot_nik');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('screenshot_nik', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['frequency_save_screenshot'])) {
			$data['error_frequency_save_screenshot'] = $this->error['frequency_save_screenshot'];
		} else {
			$data['error_frequency_save_screenshot'] = '';
		}

		if (isset($this->error['date_clear_folder'])) {
			$data['error_date_clear_folder'] = $this->error['date_clear_folder'];
		} else {
			$data['error_date_clear_folder'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/screenshot_nik', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/screenshot_nik', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/screenshot_nik', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/screenshot_nik', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

        if (isset($this->request->post['layout_name'])) {
            $data['layout_name'] = $this->request->post['layout_name'];
        } elseif (!empty($module_info)) {
            $data['layout_name'] = $module_info['layout_name'];
        } else {
            $data['layout_name'] = '';
        }

		if (isset($this->request->post['frequency_save_screenshot'])) {
			$data['frequency_save_screenshot'] = $this->request->post['frequency_save_screenshot'];
		} elseif (!empty($module_info)) {
			$data['frequency_save_screenshot'] = $module_info['frequency_save_screenshot'];
		} else {
			$data['frequency_save_screenshot'] = '';
		}

		if (isset($this->request->post['frequency_save_screenshot_unit'])) {
			$data['frequency_save_screenshot_unit'] = $this->request->post['frequency_save_screenshot_unit'];
		} elseif (!empty($module_info)) {
			$data['frequency_save_screenshot_unit'] = $module_info['frequency_save_screenshot_unit'];
		} else {
			$data['frequency_save_screenshot_unit'] = '';
		}

		if (isset($this->request->post['date_clear_folder'])) {
			$data['date_clear_folder'] = $this->request->post['date_clear_folder'];
		} elseif (!empty($module_info)) {
			$data['date_clear_folder'] = $module_info['date_clear_folder'];
		} else {
			$data['date_clear_folder'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['clearAllScreenshots'] = $this->url->link('extension/module/screenshot_nik/clearSavedScreenshots', 'user_token=' . $this->session->data['user_token'], true);

        $this->load->model('design/layout');

        $layouts_total = $this->model_design_layout->getTotalLayouts();

        $filter_data = array(
            'start' => 0,
            'limit' => $layouts_total
        );

        $data['layouts'] = $this->model_design_layout->getLayouts($filter_data);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/screenshot_nik', $data));
	}

	public function install() {
        $this->load->model('design/layout');

        $upload_dir_path = DIR_IMAGE . "screenshot_users";

        if( ! is_dir( $upload_dir_path ) ) mkdir( $upload_dir_path, 0777 );

        $layouts_total = $this->model_design_layout->getTotalLayouts();

        $filter_data = array(
            'start' => 0,
            'limit' => $layouts_total
        );

        $layouts = $this->model_design_layout->getLayouts($filter_data);

        if (!empty($layouts)) {
            foreach ($layouts as $layout) {
                $path = $upload_dir_path . "/" . utf8_strtolower($layout['name']);

                if( ! is_dir( $path ) ) mkdir( $path, 0777 );
            }
        }
    }

    public function clearSavedScreenshots() {
        $this->load->model('setting/module');

        $modules = $this->model_setting_module->getModulesByCode('screenshot_nik');

        date_default_timezone_set('Europe/Moscow');

        foreach ($modules as $module) {
            $settings = json_decode($module['setting'], true);

            if($settings['date_clear_folder'] == date('j')) {
                $dir = DIR_IMAGE . "screenshot_users/" . $settings['layout_name'];

                $this->deleteDirectory($dir);

                if( ! is_dir( $dir ) ) mkdir( $dir, 0777 );
            }
        }

        $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
    }

    private function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return @rmdir($dir);
    }

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/html')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if ((utf8_strlen($this->request->post['frequency_save_screenshot']) < 1)) {
			$this->error['frequency_save_screenshot'] = $this->language->get('error_frequency_save_screenshot');
		}

        if (!empty($this->request->post['date_clear_folder'])) {
            if ((int)$this->request->post['date_clear_folder'] < 1 || (int)$this->request->post['date_clear_folder'] > 31) {
                $this->error['date_clear_folder'] = $this->language->get('error_date_clear_folder');
            }
        }

		return !$this->error;
	}
}