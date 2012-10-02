<?php

class ComValidationCommandValidate extends KCommand
{
	public function _controllerBeforeAdd(KCommandContext $context)
	{

		$model = $context->caller->getModel();
		$item = $model->getItem();

        if( $item )
        {
            $identifier = (string) $item->getIdentifier();

            $item->setData(KConfig::unbox($context->data));

            if($item->isValidatable())
            {
                if(!$item->validate())
                {
                    //Store the data in the session to pre-populate the model item
                    KRequest::set('session.data.'.$identifier, array($item->getIterator()) );
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