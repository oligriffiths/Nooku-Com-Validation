<?php

class ComValidationCommandValidate extends KCommand
{
	public function _controllerBeforeAdd(KCommandContext $context)
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
						$query = $referrer->getQuery(true);
						$query['id'] = $item->id;
						$referrer->setQuery($query);
						$context->caller->setRedirect((string)$referrer );
					}

					return false;
				}
			}
		}

		return true;
	}

	public function _controllerBeforeEdit(KCommandContext $context)
	{
		return $this->_controllerBeforeAdd($context);
	}
}