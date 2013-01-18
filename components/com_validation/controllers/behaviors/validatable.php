<?php
/**
 * Created By: Oli Griffiths
 * Date: 05/11/2012
 * Time: 11:25
 */
defined('KOOWA') or die('Protected resource');

class ComValidationControllerBehaviorValidatable extends KControllerBehaviorAbstract
{
	protected $_redirect;

	/**
	 * Executes the validate action before any "save" events and raises errors on redirect
	 * @param $name
	 * @param KCommandContext $context
	 * @return bool
	 */
	public function execute($name, KCommandContext $context)
	{
		if(in_array($name, array('before.add','before.edit'))){
			return $this->validate($context);
		}

		if(in_array($name, array('after.add','after.edit','after.apply','after.save'))){
			$this->setRedirect();
		}

		if(in_array($name, array('after.validate')))
		{
			$this->raiseErrors($context);
		}
		return parent::execute($name, $context);
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
		return KMixinAbstract::getHandle();
	}


	/**
	 * Runs the validation on the row, and sets the redirect to the referrer if validation fails
	 * @param KCommandContext $context
	 * @return bool
	 */
	protected function _actionValidate(KCommandContext $context)
	{
		$model = $context->caller->getModel();
		$item = $model->getItem();
		$this->_redirect = null;

		if( $item )
		{
			if($item->isValidatable())
			{
				$item->setData(KConfig::unbox($context->data));

				try{
					$item->validate();
				}catch(Exception $e)
				{
					$referrer = KRequest::referrer();
					if($referrer){
						$this->_redirect = (string) $referrer;
					}

					return false;
				}
			}
		}

		return true;
	}


	/**
	 * Sets the redirect in the mixer
	 * This has to be called afterSave/Apply as those methods set the redirect also
	 */
	protected function setRedirect()
	{
		if($this->_redirect){
			$this->getMixer()->setRedirect($this->_redirect);
		}
	}


	/**
	 * Raises any errors that the row contains
	 * @param KCommandContext $context
	 */
	protected function raiseErrors(KCommandContext $context)
	{
		$model = $context->caller->getModel();
		$item = $model->getItem();

		if ($item->isValidatable()) {
	        $errors = (array) $item->getValidationErrors();

            $text = '';
            $isHtml = KRequest::format() == 'html';
	        $identifier = $item->getIdentifier();

		    foreach($errors AS $key => $error)
		    {
			    foreach($error AS $e){
                    if($isHtml){
                        $msg = 'Error: ('.KInflector::humanize($key).') - '.$e;
                        if(class_exists('KMessage')){
                            KMessage::setMessage($msg, 'error', $identifier, $key);
                        }else if(class_exists('JApplication')){
                            JFactory::getApplication()->enqueueMessage($msg,'error');
                        }
                    }else{
                        $text .= 'Validation error: ('.$key.') : '.$e.' -- ';
                    }
			    }
            }
            if(!$isHtml) $this->setResponse($context, KHttpResponse::BAD_REQUEST, $text);
		}
	}


	/**
	 * Sets the response in the context.
	 * This is a 12.2/12.3 compatibility method
	 *
	 * @param KCommandContext $context
	 * @param $code
	 * @param $message
	 * @param array $headers
	 */
	protected function setResponse(KCommandContext $context, $code, $message, $headers = array())
    {
        if($context->response){
            if(!$context->response->getStatus()){
                $context->response->setStatus(
                    $code, $message
                );

                foreach($headers AS $key => $msg) $context->response->headers->set($key, $msg);
            }
        }else{
            if(!$context->getError()){
                $context->setError(new KControllerException(
                    $message, $code
                ));

                $context->headers = array_merge(is_array($context->headers) ? $context->headers : array(), $headers);
            }
        }
    }
}