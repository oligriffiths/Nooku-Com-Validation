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

        if(in_array($command->getName(), array('after.validate')))
        {
            $this->raiseErrors($command);
        }

		if(in_array($command->getName(), array('after.add','after.edit','after.apply','after.save'))){
			$this->setRedirect($command);
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
	protected function _actionValidate(Library\ControllerContextInterface $context)
	{
		$model = $context->getSubject()->getModel();
		$entity = $model->fetch();
		$this->_redirect = null;

		if( $entity )
		{
			if($entity->isValidatable())
			{
                $entity->setProperties($context->request->data->toArray());

                if($context->getSubject()->isDispatched()){

                    try{
                        $entity->validate();
                    }catch(\Exception $e)
                    {
                        $referrer = $context->getRequest()->getReferrer();
                        if($referrer){
                            $this->_redirect = $referrer;
                        }

                        return false;
                    }

                }else{
                    $entity->validate();
                }
			}
		}

		return true;
	}


	/**
	 * Sets the redirect in the mixer
	 * This has to be called afterSave/Apply as those methods set the redirect also
	 */
	protected function setRedirect(Library\ControllerContextInterface $context)
	{
		if($this->_redirect){
            $context->getResponse()->setRedirect($this->_redirect, null);
		}
	}


	/**
	 * Raises any errors that the row contains
	 * @param Library\CommandContext $context
	 */
	protected function raiseErrors(Library\ControllerContextInterface $context)
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
     * Mixin the behvior to the row, and load previous data from the session if set
     *
     * @param Library\DatabaseContext $context
     */
//	protected function _afterTableSelect(Library\DatabaseContext $context)
//	{
//		$data = $context->data;
//		if($data instanceof KDatabaseRowAbstract || $data instanceof KDatabaseRowsetAbstract)
//		{
//			if($data->isValidatable())
//			{
//				$identifier = $this->getIdentifier()->toArray();
//				$identifier['path'] = null;
//				$identifier['name'] = null;
//
//				if($data instanceof KDatabaseRowAbstract) $data = array($data);
//
//				//Get the rows primary key columns ot build identifier
//				$identifier = clone $context->data->getIdentifier();
//				$identifier->path = array('database','row');
//				$identifier->name = KInflector::singularize($identifier->name);
//				$identifier = (string) $identifier;
//
//				//Check if there is session data for this identifier root
//				if($hasSessionData = KRequest::has('session.data.'.$identifier,'raw')){
//					foreach($data AS $row)
//					{
//						//Ensure behavior is mixed in
//						if($row->isValidatable())
//						{
//							$this->loadFromSession($row);
//						}
//					}
//				}
//			}
//		}
//	}

//	/**
//	 * Loads a rows data from the session
//	 *
//	 * @param null|KDatabaseRowInterface $row
//	 * @return DatabaseBehaviorValidatable
//	 */
//	public function loadFromSession($row = null, $clear = true)
//	{
//		$row = $row ? $row : $this->getMixer();
//
//		//Get the rows primary key columns ot build identifier
//		$identifier = (string) $row->getIdentifier();
//
//		//Compile primary keys
//		foreach($row->getTable()->getUniqueColumns() AS $column_id => $column) if($column->primary) $identifier .= '.'.$row->get($column_id);
//
//		//Retrieve the data in the session to pre-populate the row
//		if($prev_data = KRequest::get('session.data.'.$identifier, 'raw'))
//		{
//			$row_data = $row->getData();
//			if(array_intersect_key($row_data, $prev_data) == $row_data)
//			{
//				$row->setData($prev_data);
//
//				//Clear session data
//				if($clear) KRequest::set('session.data.'.$identifier, null);
//			}
//		}
//
//		return $this;
//	}
//
//
//	/**
//	 * Stores a rows data in the session
//	 * @param null|KDatabaseRowInterface $row
//	 */
//	public function storeToSession($row = null)
//	{
//		$row = $row ? $row : $this->getMixer();
//
//		//Construct object identifier
//		$identifier = (string) $row->getIdentifier();
//
//		//Add the rows identifers
//		foreach($row->getTable()->getUniqueColumns() AS $column_id => $column) if($column->primary) $identifier .= '.'.$row->get($column_id);
//
//		//Casting as a Library\ObjectConfig and to array will convert and sub Library\ObjectConfigs back to arrays
//		$data = new Library\ObjectConfig($row->getData());
//		$data = $data->toArray();
//
//		//Remove any objects
//		array_walk_recursive($data, array($this, 'removeLibrary\Objects'));
//
//		KRequest::set('session.data.'.$identifier, $data);
//	}
//
//
//	/**
//	 * Removes objects from an array
//	 * @param $entity
//	 * @param $key
//	 */
//	protected function removeObjects(&$entity, $key)
//	{
//		if($entity instanceof Library\Object) $entity = null;
//	}
//
//
//	/**
//	 * Stores a rows data in the session
//	 * @param null|KDatabaseRowInterface $row
//	 */
//	public function removeFromSession($row = null)
//	{
//		$row = $row ? $row : $this->getMixer();
//
//		//Construct object identifier
//		$identifier = (string) $row->getIdentifier();
//
//		//Add the rows identifers
//		foreach($row->getTable()->getUniqueColumns() AS $column_id => $column) if($column->primary) $identifier .= '.'.$row->get($column_id);
//
//		KRequest::set('session.data.'.$identifier, null);
//	}

}