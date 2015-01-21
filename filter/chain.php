<?php
/**
 * Created By: Oli Griffiths
 * Date: 11/12/2012
 * Time: 12:24
 */
namespace Oligriffiths\Component\Validation;

use Nooku\Library;

class FilterChain extends Library\FilterChain
{
    /**
     * Adds an array of filters, creating filters from strings if necessary
     *
     * @param array $filters
     * @return $this
     */
    public function addFilters(array $filters)
    {
        foreach($filters AS $key => $filter){

            $params = array();

            //If filter is an array, the key is the name, $filter is params
            if(is_array($filter)){
                $params = $filter;
                $filter = $key;
            }

            //If filter is a string, convert to an identifier & instantiate the filter
            if(is_string($filter)){
                $identifier = $filter;

                if(strpos($filter, '.') === false){
                    $identifier = $this->getIdentifier()->toArray();
                    $identifier['name'] = $filter;
                }

                $filter = $this->getObject($identifier, $params);
            }

            //Get priority from params
            $priority = isset($params['priority']) ? $params['priority'] : $filter->getPriority();
            unset($params['priority']);

            $this->addFilter($filter, $priority);
        }

        return $this;
    }

    /**
     * Removes a filter from the queue based
     *
     * @param FilterInterface 	$filter A Filter
     *
     * @return FilterChain
     */
    public function removeFilter(Library\FilterInterface $filter)
    {
        $this->_queue->dequeue($filter);

        return $this;
    }

    /**
     * Returns the number of filters in the chain
     *
     * @return int
     */
    public function count()
    {
        return $this->_queue->count();
    }
}