<?php
/**
 * Created By: Oli Griffiths
 * Date: 05/11/2012
 * Time: 11:25
 */
defined('KOOWA') or die('Protected resource');

class ComValidationControllerBehaviorValidatable extends KControllerBehaviorAbstract
{
	/**
	 * Executes the validate action before any "save" events and raises errors on redirect
	 * @param $name
	 * @param KCommandContext $context
	 * @return bool
	 */
	public function execute($name, KCommandContext $context)
	{
		if(in_array($name, array('before.add','before.edit','before.apply','before.save'))){
			return $this->validate($context);
		}

		if(in_array($name, array('after.get')))
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

		if( $item )
		{
			$item->setData(KConfig::unbox($context->data));
			if($item->isValidatable())
			{
				try{
					$item->validate();
				}catch(Exception $e)
				{
					//Redirect to the referring page
					$referrer = KRequest::referrer();
					if($referrer){
						$query = $referrer->query;
						$query['id'] = $item->id;
						$referrer->query = $query;
						$context->caller->setRedirect((string)$referrer );
					}

                    $errors = (array) $item->getValidationErrors();
                    $text = '';
                    foreach($errors AS $column => $error)
                    {
                        foreach($error AS $e)
                        {
                            $text .= 'Validation error: ('.$column.') : '.$e.' -- ';
                        }
                    }
                    $this->setResponse($context, KHttpResponse::BAD_REQUEST, $text);

					return false;
				}
			}
		}

		return true;
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

		    foreach($errors AS $key => $error)
		    {
			    foreach($error AS $e){
				    $msg = 'Error: ('.KInflector::humanize($key).') - '.$e;
				    if(class_exists('KMessage')){
					    KMessage::setMessage($msg, 'error', $item->getIdentifier(), $key);
				    }else if(class_exists('JApplication')){
					    JFactory::getApplication()->enqueueMessage($msg,'error');
				    }
			    }
            }
		}
	}


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