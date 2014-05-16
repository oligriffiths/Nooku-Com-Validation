<?php
/**
 * Created By: Oli Griffiths
 * Date: 05/11/2012
 * Time: 11:25
 */
namespace Nooku\Component\Validation;

use Nooku\Library;

class ControllerBehaviorValidatable extends Library\ControllerBehaviorAbstract
{
	protected $_redirect;

	/**
	 * Executes the validate action before any "save" events and raises errors on redirect
	 * @param $name
	 * @param Library\CommandContext $context
	 * @return bool
	 */
	public function execute(Library\CommandInterface $command, Library\CommandChainInterface $chain)
	{
		if(in_array($command->getName(), array('before.add','before.edit'))){
			return $this->validate($command);
		}

		if(in_array($command->getName(), array('after.add','after.edit','after.apply','after.save'))){
			$this->setRedirect($command);
		}

        if(in_array($command->getName(), array('after.read'))){
            $this->restoreSessionData($command);
        }

		return parent::execute($command, $chain);
	}


	/**
	 * Get an object handle
	 *
	 * Force the object to be enqueue in the command chain.
	 *
	 * @return string A string that is unique, or NULL
	 * @see execute()
	 */
	public function getHandle()
	{
        return spl_object_hash($this);
	}


	/**
	 * Runs the validation on the row, and sets the redirect to the referrer if validation fails
	 * @param Library\CommandContext $context
	 * @return bool
	 */
	protected function validate(Library\CommandInterface $context)
	{
		$model = $context->getSubject()->getModel();
		$entity = $model->fetch();
		$this->_redirect = null;

		if( $entity )
		{
			if($entity->isValidatable())
			{
                $data = $context->request->data->toArray();
                $entity->setProperties($data);

                if($context->getSubject()->isDispatched()){

                    try{
                        $entity->validate();
                    }catch(\Exception $e)
                    {
                        $referrer = $context->getRequest()->getReferrer();
                        if($referrer){
                            $this->_redirect = $referrer;
                        }

                        $this->raiseErrors($context);
                        $this->storeSessionData($context);

                        return false;
                    }

                }else{
                    $entity->validate();
                }
			}
		}

        if($context->getSubject()->isDispatched()){
            $this->clearSessionData($context);
        }

		return true;
	}


	/**
	 * Sets the redirect in the mixer
	 * This has to be called afterSave/Apply as those methods set the redirect also
	 */
	protected function setRedirect(Library\CommandInterface $context)
	{
		if($this->_redirect){
            $context->getResponse()->setRedirect($this->_redirect, null);
		}
	}


	/**
	 * Raises any errors that the row contains
	 * @param Library\CommandContext $context
	 */
	protected function raiseErrors(Library\CommandInterface $context)
	{
		$model = $context->getSubject()->getModel();
		$entity = $model->fetch();

		if ($entity->isValidatable()) {
	        $errors = (array) $entity->getValidationErrors();

            $text = '';
            $isHtml = $context->request->getFormat() == 'html';

		    foreach($errors AS $error)
		    {
			    foreach($error AS $e)
                {
                    $msg = 'Error: '.$e;

                    if($isHtml){
                        $context->getResponse()->addMessage($msg, Library\ControllerResponseInterface::FLASH_ERROR);
                    }else{
                        $text .= $msg;
                    }
			    }
            }

            if(!$isHtml) $context->getResponse()->setStatus(Library\HttpResponse::BAD_REQUEST, $text);
		}
	}


    /**
     * Stores entity data in the session so it can be restored on redirect
     * @param Library\CommandInterface $context
     */
    protected function storeSessionData(Library\CommandInterface $context)
    {
        $model = $context->getSubject()->getModel();
        $entity = $model->fetch();

        if ($entity->isValidatable()) {

            //Get the data from the entity
            $data = $entity->top()->toArray();

            //Start the session (if not started already)
            $session = $context->getUser()->getSession();

            //Start the session if not already started
            $session->start();

            //Store the entity data
            $session->set('validation.'.str_replace('.','-',$entity->getIdentifier()).'.'.$entity->id, $data);
        }
    }


    /**
     * Restores session data into the entity
     * @param Library\CommandInterface $context
     */
    protected function restoreSessionData(Library\CommandInterface $context)
    {
        $model = $context->getSubject()->getModel();
        $entity = $model->fetch();

        if ($entity->isValidatable()) {

            //Start the session (if not started already)
            $session = $context->getUser()->getSession();

            //Start the session if not already started
            $session->start();

            if($data = $session->get('validation.'.str_replace('.','-',$entity->getIdentifier()).'.'.$entity->id)){

                $entity->setProperties($data, false);
            }
        }
    }


    /**
     * Clears the session data for the entity on successful validation
     * @param Library\CommandInterface $context
     */
    protected function clearSessionData(Library\CommandInterface $context)
    {
        $model = $context->getSubject()->getModel();
        $entity = $model->fetch();

        if ($entity->isValidatable()) {

            //Start the session (if not started already)
            $session = $context->getUser()->getSession();

            //Start the session if not already started
            $session->start();

            //Store the entity data
            $session->remove('validation.'.str_replace('.','-',$entity->getIdentifier()).'.'.$entity->id);
        }
    }
}