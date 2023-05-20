<?php

class Items extends Controller
{

    private $error = [];

    public function __construct()
    {
        $this->itemsModel = $this->model('Item');
    }


    public function get($id)
    {
        if (!$this->validate(['id' => $id], 'get')) {
            throw new ValidationException($this->error, 'Validation error', 400);
        }
        $item = $this->itemsModel->getItemById($id);
        if ($item) {
            $response = [
                'data' => (array)$item
            ];
            echo json_encode($response);
        } else {
            throw new CustomException('Item with received id not found', 404);
        }
    }

    public function create()
    {
        $data = $_POST;
        if ($this->validate($data, 'create')) {
            if ($this->itemsModel->addItem($data)) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 201 Created', true, 201);
            } else {
                throw new CustomException('Error item creation', 500);
            }
        } else {
            throw new ValidationException($this->error, 'Validation error', 400);
        }
    }

    public function update($id)
    {
        parse_str(file_get_contents("php://input"), $data);
        $data['id'] = $id;

        if ($this->validate($data, 'update')) {
            $item = $this->itemsModel->getItemById($id);
            if ($item) {
                if ($this->itemsModel->updateItem($data)) {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 204 No Content', true, 204);
                } else {
                    throw new CustomException('Error item update', 500);
                }
            } else {
                throw new CustomException('Update fail: item with received id not found', 404);
            }
        } else {
            throw new ValidationException($this->error, 'Validation error', 400);
        }
    }

    public function updatePartial($id)
    {
        parse_str(file_get_contents("php://input"), $data);

        // filter input data for only existing fields
        $data = array_filter($data, function ($k) {
            return in_array($k, ['name', 'key', 'phone']);
        }, ARRAY_FILTER_USE_KEY);
        $data['id'] = $id;

        if ($this->validate($data, 'update_partial')) {
            $item = $this->itemsModel->getItemById($id);
            if ($item) {
                if ($this->itemsModel->updateItemPartial($data)) {
                    header($_SERVER['SERVER_PROTOCOL'] . ' 204 No Content', true, 204);
                } else {
                    throw new CustomException('Error item update', 500);
                }
            } else {
                throw new CustomException('Update fail: item with received id not found', 404);
            }
        } else {
            throw new ValidationException($this->error, 'Validation error', 400);
        }
    }


    public function delete($id)
    {
        if (!$this->validate(['id' => $id], 'get')) {
            throw new ValidationException($this->error, 'Validation error', 400);
        }
        $item = $this->itemsModel->getItemById($id);
        if ($item) {
            if ($this->itemsModel->deleteItem($id)) {
                header($_SERVER['SERVER_PROTOCOL'] . ' 204 No Content', true, 204);
            } else {
                throw new CustomException('Error item delete', 500);
            }
        } else {
            throw new CustomException('Delete fail: item with received id not found', 404);
        }

    }

    private function validate($data, $action)
    {
        $required_fields = [
            'name',
            'key',
            'phone'
        ];
        switch ($action) {
            case 'get':
                if (!preg_match("/^[0-9]+$/", $data['id'])) {
                    $this->error[] = [
                        'field' => 'id',
                        'message' => 'Field value must be a number'
                    ];
                    return false;
                }
                break;
            case 'create':
                foreach ($required_fields as $required) {
                    if (!isset($data[$required])) {
                        $this->error[] = [
                            'field' => $required,
                            'message' => "Field {$required} is required"
                        ];
                    }
                }
                if (isset($data['name'])) {
                    if ((mb_strlen(trim($data['name'])) < 1) || (mb_strlen(trim($data['name'])) > 255)) {
                        $this->error[] = [
                            'field' => 'name',
                            'message' => 'Field name must be greater then 1 character and less then 255'
                        ];
                    }
                }
                if (isset($data['key'])) {
                    if ((mb_strlen(trim($data['key'])) < 1) || (mb_strlen(trim($data['key'])) > 25)) {
                        $this->error[] = [
                            'field' => 'key',
                            'message' => 'Field key must be greater then 1 character and less then 255'
                        ];
                    }
                }
                if (isset($data['phone'])) {
                    if ((mb_strlen(trim($data['phone'])) < 1) || (mb_strlen(trim($data['phone'])) > 15)) {
                        $this->error[] = [
                            'field' => 'phone',
                            'message' => 'Field phone must be greater then 1 character and less then 255'
                        ];
                    }
                }
                if ($this->error) {
                    return false;
                }
                break;

            case 'update':
                if (!preg_match("/^[0-9]+$/", $data['id'])) {
                    $this->error[] = [
                        'field' => 'id',
                        'message' => 'Field value must be a number'
                    ];
                }
                foreach ($required_fields as $required) {
                    if (!isset($data[$required])) {
                        $this->error[] = [
                            'field' => $required,
                            'message' => "Field {$required} is required"
                        ];
                    }
                }
                if (isset($data['name'])) {
                    if ((mb_strlen(trim($data['name'])) < 1) || (mb_strlen(trim($data['name'])) > 255)) {
                        $this->error[] = [
                            'field' => 'name',
                            'message' => 'Field name must be greater then 1 character and less then 255'
                        ];
                    }
                }
                if (isset($data['key'])) {
                    if ((mb_strlen(trim($data['key'])) < 1) || (mb_strlen(trim($data['key'])) > 25)) {
                        $this->error[] = [
                            'field' => 'key',
                            'message' => 'Field key must be greater then 1 character and less then 255'
                        ];
                    }
                }
                if (isset($data['phone'])) {
                    if ((mb_strlen(trim($data['phone'])) < 1) || (mb_strlen(trim($data['phone'])) > 15)) {
                        $this->error[] = [
                            'field' => 'phone',
                            'message' => 'Field phone must be greater then 1 character and less then 255'
                        ];
                    }
                }
                if ($this->error) {
                    return false;
                }
                break;
            case 'update_partial':
                if (!preg_match("/^[0-9]+$/", $data['id'])) {
                    $this->error[] = [
                        'field' => 'id',
                        'message' => 'Field value must be a number'
                    ];
                }
                $find = 0;
                foreach ($required_fields as $required) {
                    if (array_key_exists($required, $data)) {
                        $find = 1;
                        break;
                    }
                }
                if (!$find) {
                    throw new ValidationException([], 'PATCH must contain one of the `item` entity properties (name, phone, key)', 400);
                }
                if (isset($data['name'])) {
                    if ((mb_strlen(trim($data['name'])) < 1) || (mb_strlen(trim($data['name'])) > 255)) {
                        $this->error[] = [
                            'field' => 'name',
                            'message' => 'Field name must be greater then 1 character and less then 255'
                        ];
                    }
                }
                if (isset($data['key'])) {
                    if ((mb_strlen(trim($data['key'])) < 1) || (mb_strlen(trim($data['key'])) > 25)) {
                        $this->error[] = [
                            'field' => 'key',
                            'message' => 'Field key must be greater then 1 character and less then 255'
                        ];
                    }
                }
                if (isset($data['phone'])) {
                    if ((mb_strlen(trim($data['phone'])) < 1) || (mb_strlen(trim($data['phone'])) > 15)) {
                        $this->error[] = [
                            'field' => 'phone',
                            'message' => 'Field phone must be greater then 1 character and less then 255'
                        ];
                    }
                }
                if ($this->error) {
                    return false;
                }
                break;
        }
        return true;
    }

}
