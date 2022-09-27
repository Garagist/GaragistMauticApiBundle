<?php

namespace MauticPlugin\GaragistMauticApiBundle\Controller\Api;

use Mautic\ApiBundle\Controller\CommonApiController;
use Symfony\Component\HttpFoundation\Response;
use Mautic\CoreBundle\Model\AbstractCommonModel;

class EmailApiController extends CommonApiController
{
    const EXAMPLE_EMAIL_SUBJECT_PREFIX = '[TEST]';

    /**
     * Send example emails to recipients
     *
     * @return Response
     */
    public function exampleEmailAction($id)
    {
        if ('POST' == $this->request->getMethod()) {
            $recipients = $this->request->request->get('recipients');

            // Note: copy from https://github.com/mautic/mautic/blob/34cb7061f10df17c919a32030480a8a781553776/app/bundles/EmailBundle/Controller/EmailController.php#L1363
            // Prepare a fake lead
            $model  = $this->getModel('email');
            $entity = $model->getEntity($id);

            // We have to add prefix to example emails
            $subject = sprintf('%s %s', static::EXAMPLE_EMAIL_SUBJECT_PREFIX, $entity->getSubject());
            $entity->setSubject($subject);

            /** @var \Mautic\LeadBundle\Model\FieldModel $fieldModel */
            $fieldModel = $this->getModel('lead.field');
            $fields     = $fieldModel->getFieldList(false, false);
            array_walk(
                $fields,
                function (&$field) {
                    $field = "[$field]";
                }
            );
            $fields['id'] = 0;

            $errors = [];
            foreach ($recipients as $email) {
                if (!empty($email)) {
                    $users = [
                        [
                            // Setting the id to null as this is a unknown user
                            // Set firstname and lastname to Firstname and Lastname to test the Dynamic Web Content
                            'id'        => '',
                            'firstname' => 'Firstname',
                            'lastname'  => 'Lastname',
                            'email'     => $email,
                        ],
                    ];

                    // Send to current user
                    $error = $model->sendSampleEmailToUser($entity, $users, $fields, [], [], false);
                    if (count($error)) {
                        array_push($errors, $error[0]);
                    }
                }
            }
        }

        if (0 != count($errors)) {
            $result = [
                'success'    => 0,
                'recipients' => $recipients
            ];
        } else {
            $result = [
                'success'    => 1,
                'recipients' => $recipients
            ];
        }

        $view = $this->view(
            $result,
            Response::HTTP_OK
        );

        return $this->handleView($view);
    }

    /**
     * Get a model instance from the service container.
     *
     * @param $modelNameKey
     *
     * @return AbstractCommonModel
     */
    protected function getModel($modelNameKey)
    {
        return $this->get('mautic.model.factory')->getModel($modelNameKey);
    }
}
