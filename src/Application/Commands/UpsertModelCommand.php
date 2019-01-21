<?php

/**
 * updates or inserts on db the data
 */
namespace Osds\Api\Application\Commands;

use Osds\Api\Infrastructure\Repositories\S3Repository;
use Ramsey\Uuid\Uuid;

class UpsertModelCommand extends BaseCommand
{

    public function execute()
    {
        $args = $this->request->parameters;
        unset($args['uri_params']);

        foreach($args as $key => $value) {
            #we want to avoid filtering for this field (maybe empty)
            if($value === 'DB_NULL') {
                $args[$key] = null;
            }

            if($key === 'files')
            {
                foreach ($value as $file => $file_data) {

                    // Check if Key has '##' => then it is a file that needs persistence
                    if (preg_match('/(.*)##(.*)\|(.*)$/', $file, $res)) {

                        $field_name = $res[1];
                        $repository = $res[2];
                        $path_folder = $res[3];

                        try {
                            $persisted = false;
                            switch ($repository) {
                                case 'S3':
                                    $extension = pathinfo($file_data['name'], PATHINFO_EXTENSION);
                                    $filename = Uuid::uuid1()->toString() . '.' . $extension;
                                    $args[$field_name] = S3Repository::persist($this->services['awss3util'], $filename, $file_data['tmp_name'], ['folder' => $path_folder]);
                                    $persisted = true;
                                    break;
                            }

                            if (!$persisted) {
                                throw new \Exception('No persistence valid method specified for ' . $file . ' (' . json_encode($file_data) . ')');
                            }
                        } catch (\Exception $e) {
                            return ['error_message' => $e->getMessage()];
                        }
                    }
                }

                unset($args['files']);
            }
            else if (is_array($value))
            {
                $args[$key] = json_encode($value);
            }
        }

        $this->request->custom_parameters->entity_id = $this->repository->upsert($this->request->custom_parameters->entity_id, $args);

        return [
            'upsert_id' => $this->request->custom_parameters->entity_id
        ];
    }

}