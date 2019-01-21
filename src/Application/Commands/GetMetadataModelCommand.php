<?php

/**
 * Gets the db fields for this model and their possible relations (searched by db foreign keys) with other models
 */
namespace Osds\Api\Application\Commands;

class GetMetadataModelCommand extends BaseCommand
{

    /**
     *
     * @return array
     */
    public function execute($model = null)
    {
        if($model != null)
        {
            $this->repository->setEntity($model);
        }

        $data = [];
        $model_data = [];

        #Get Model possible metadata attributes
        $entity = 'App\Entity\\' . $this->repository->getModel();
        $helper_class = new \ReflectionClass($entity);
        if($helper_class->hasProperty('metadata_attributes'))
        {
            $class_constants = $helper_class->getConstants();
            $metadata_attributes = $helper_class->getProperty('metadata_attributes')->getValue(new $entity);

            foreach($metadata_attributes as $metadata_attribute_type)
            {
                $metadata_attribute_constants = preg_grep('/' . $metadata_attribute_type . '/', array_keys($class_constants));

                foreach($metadata_attribute_constants as $ma_constant)
                {
                    $value = $class_constants[$ma_constant];
                    $ma_constant_name = str_replace($metadata_attribute_type . '_', '', $ma_constant);

                    #make it nice
                    $nice_name = ucfirst(strtolower(str_replace("_", " ", str_replace("{$metadata_attribute_type}_", "", $ma_constant_name))));
                    $model_data[strtolower($metadata_attribute_type)]['by_id'][$value] = $nice_name;
                    $model_data[strtolower($metadata_attribute_type)]['by_name'][$nice_name] = $value;
                }
            }

        }

        $debug_backtrace = debug_backtrace();
        if(
            #check it's not recursive
            isset($debug_backtrace[1])
            &&
            isset($debug_backtrace[1]['class'])
            &&
            $debug_backtrace[1]['class'] != self::class
        )
        {
            $data[strtolower($this->repository->getModel())] = $model_data;

            if(isset($this->args['related_models']))
            {
                $related_models = explode(',', $this->args['related_models']);
                foreach($related_models as $rm)
                {
                    $data[strtolower($rm)] = $this->execute($rm);
                }
            }
            return $data;

        } else {
            #it's rercursive => return only this data
            return $model_data;
        }

    }

}