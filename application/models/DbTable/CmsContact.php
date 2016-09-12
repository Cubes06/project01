<?php

class Application_Model_DbTable_CmsContact extends Zend_Db_Table_Abstract {
	
	
	const STATUS_ENABLED = 1;
	const STATUS_DISABLED = 0;
	
	protected $_name = 'cms_contact';
	
	/**
	 * @param int $id
	 * @return null|array Associative array with keys as cms_contact table columns or NULL if not found
	 */
	public function getContactById($id) {
		
		$select = $this->select();
		$select->where('id = ?', $id);
		
		$row = $this->fetchRow($select);
		
		if ($row instanceof Zend_Db_Table_Row) {
			
			return $row->toArray();
		} else {
			// row is not found
			return null;
		}
	}
	
	
	/**
	 * @param array $contact Associative array with keys as column names and values as coumn new values
	 * @return int ID of new contact
	 */
	public function insertContact($contact) {
		
		
		return $this->insert($contact);
	}
	
	/**
	 * @param int $id
	 * @param array $contact Associative array with keys as column names and values as coumn new values
	 */
	public function updateContact($id, $contact) {
		
		if (isset($contact['id'])) {
			//Forbid changing of contact id
			unset($contact['id']);
		}
		
		$this->update($contact, 'id = ' . $id);
	}
	
	
	/**
	 * 
	 * @param int $id ID of contact to delete
	 */
	public function deleteContact($id) {
		
		$this->delete('id = ' . $id);
	}
	
	/**
	 * 
	 * @param int $id ID of contact to disable
	 */
	public function disableContact($id) {
		
		$this->update(array(
			'status' => self::STATUS_DISABLED
		), 'id = ' . $id);
	}
	
	/**
	 * 
	 * @param int $id ID of contact to enable
	 */
	public function enableContact($id) {
		
		$this->update(array(
			'status' => self::STATUS_ENABLED
		), 'id = ' . $id);
	}
	
	/**
	 * Array $parameters is keeping search parameters.
	 * Array $parameters must be in following format:
	 *		array(
	 *			'filters' => array(
	 *				'status' => 1,
	 *				'id' => array(3, 8, 11)
	 *			),
	 *			'orders' => array(
	 *				'contactname' => 'ASC', // key is column , if value is ASC then ORDER BY ASC,
	 *				'first_name' => 'DESC', // key is column, if value is DESC then ORDER BY DESC
	 *			),
	 *			'limit' => 50, //limit result set to 50 rows
	 *			'page' => 3 // start from page 3. If no limit is set, page is ignored
	 *		)
	 * @param array $parameters Asoc array with keys "filters", "orders", "limit" and "page".
	 */
	public function search(array $parameters = array()) {
		
		$select = $this->select();
		
		if (isset($parameters['filters'])) {
			
			$filters = $parameters['filters'];
			
			$this->processFilters($filters, $select);
		}
		
		if (isset($parameters['orders'])) {
			
			$orders = $parameters['orders'];
			
			foreach ($orders as $field => $orderDirection) {
				
				switch ($field) {
					case 'id':
					case 'address':
					case 'address_number':
					case 'map_id':
					case 'phone':
					case 'fax':
					case 'email':
					case 'hours':
						
						if ($orderDirection === 'DESC') {
							
							$select->order($field . ' DESC');
						} else {
							$select->order($field);
						}
						break;
				}
			}
		}
		
		if (isset($parameters['limit'])) {
			
			if (isset($parameters['page'])) {
				// page is set do limit by page
				$select->limitPage($parameters['page'], $parameters['limit']);
			} else {
				// page is not set, just do regular limit
				$select->limit($parameters['limit']);
			}
		}
		
		//die($select->assemble());
		
		return $this->fetchAll($select)->toArray();
	}
	
	/**
	 * 
	 * @param array $filters See function search $parameters['filters']
	 * @return int Count of rows that match $filters
	 */
	public function count(array $filters = array()) {
		
		$select = $this->select();
		
		$this->processFilters($filters, $select);
		
		// reset previously set columns for resultset
		$select->reset('columns');
		// set one column/field to fetch and it is COUNT function
		$select->from($this->_name, 'COUNT(*) as total');
		
		$row = $this->fetchRow($select);
		
		return $row['total'];
	}
	
	/**
	 * Fill $select object with WHERE conditions
	 * @param array $filters
	 * @param Zend_Db_Select $select
	 */
	protected function processFilters(array $filters, Zend_Db_Select $select) {
		
		//$select object will be modified outside this function
		// object are always passed by reference
		
		foreach ($filters as $field => $value) {
				
				switch ($field) {
					
					case 'id':
					case 'address':
					case 'address_number':
					case 'map_id':
					case 'phone':
					case 'fax':
					case 'email':
					case 'hours':
						
						if (is_array($value)) {
							$select->where($field . ' IN (?)', $value);
						} else {
							$select->where($field . ' = ?', $value);
						}
						break;
					
					case 'address_search':
						
						$select->where('address LIKE ?', '%' . $value . '%');
						break;
					case 'map_id_search':
						
						$select->where('map_id LIKE ?', '%' . $value . '%');
						break;
					case 'email_search':
						
						$select->where('email LIKE ?', '%' . $value . '%');
						break;
					case 'hours_search':
						
						$select->where('hours LIKE ?', '%' . $value . '%');
						break;
					
					case 'id_exclude':
						
						if (is_array($value)) {
							
							$select->where('id NOT IN (?)', $value);
						} else {
							$select->where('id != ?', $value);
						}
						break;
					case 'address_exclude':
						
						if (is_array($value)) {
							
							$select->where('contactname NOT IN (?)', $value);
						} else {
							$select->where('contactname != ?', $value);
						}
						break;
				}
			}
	}
}