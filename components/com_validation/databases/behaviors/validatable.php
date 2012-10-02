<?php
/**
 * User: Oli Griffiths
 * Date: 02/10/2012
 * Time: 20:29
 */

class ComValidationDatabaseBehaviorValidatable extends KDatabaseBehaviorAbstract
{

	protected function _beforeTableInsert(KCommandContext $context)
	{
		return $this->_beforeTableUpdate($context);
	}

	protected function _beforeTableUpdate(KCommandContext $context)
	{
		if($context->data instanceof KDatabaseRowAbstract)
		{
			if($context->data->isValidatable())
			{
				return $context->data->validateData();
			}
		}
	}

	public function validateData()
	{
		$mixer = $this->getMixer();
		if(!$mixer instanceof KDatabaseRowAbstract)
		{
			throw new KDatabaseException(__FUNCTION__.' may only be called on a KDatabaseRow');
		}

		$rules = $mixer->getValidationRules();

		var_dump($rules);

		exit('validate');
	}


	public function getValidationRules()
	{
		$mixer = $this->getMixer();
		if(!$mixer instanceof KDatabaseRowAbstract)
		{
			throw new KDatabaseException(__FUNCTION__.' may only be called on a KDatabaseRow');
		}

		$rules = array();
		$columns = $mixer->getTable()->getColumns();
		foreach($columns AS $id => $column)
		{
			if($column->required)
			{
				$rules[$id] = array('required');
			}
		}

		return $rules;
	}
}