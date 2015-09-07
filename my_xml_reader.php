<?php

/**
 * This helper class is an extension of the PHP XMLReader class
 * It can read an XML file and convert it into an Associative Array
 * 
 * @author MR
 *
 */
class My_XML_Reader extends XMLReader
{
	protected $_xmlArray;
	protected $_xmlFullPath;

	public function resetXMLArray()
	{
		$this->_xmlArray = array();
		return $this;
	}
	
	public function getXMLPath()
	{
		return $this->_xmlFullPath;
	}
	
	public function setXMLPath($xmlPath)
	{
		if(($xmlPath = trim($xmlPath)) === '' || !file_exists($xmlPath))
			throw new Exception('The XML File Path provided ['.$xmlPath.'] is not valid');
		
		$pathParts = pathinfo($xmlPath);
		
		if(strtolower(trim($pathParts['extension'])) !== 'xml')
			throw new Exception('no XML file found for path ['.$xmlPath.']');
		
		$this->_xmlFullPath = $xmlPath;
		
		return this;
	}
	
	public function readUntilNodeFinish($nodeName, &$output)
	{
		$finished = 0;
		while($finished != 1)
		{
			if($this->nodeType == XMLReader::SIGNIFICANT_WHITESPACE)
			{
				$this->read();
				$finished = 0;
				continue;
			}
			else if($this->nodeType == XMLReader::TEXT)
			{
				$output['value'] = trim($this->value);
				$this->read();
				$finished = 0;
			}
			else if($this->nodeType == XMLReader::ELEMENT)
			{
				$tmpName = $this->localName;
	
				$counter = 0;
				if(!isset($output['value'][$tmpName]))
					$counter = 0;
				else
					$counter = count($output['value'][$tmpName]);
	
				$output['value'][$tmpName][$counter] = array();
				$output['value'][$tmpName][$counter]['value'] = array();
				$output['value'][$tmpName][$counter]['attributes'] = array();
	
				if($this->hasAttributes)
				{
					while($this->moveToNextAttribute())
						$output['value'][$tmpName][$counter]['attributes'][$this->name] = trim($this->value);
				}
	
				if(!$this->isEmptyElement)
				{
					$this->read();
					$this->readUntilNodeFinish($tmpName, $output['value'][$tmpName][$counter]);
				}
				else
				{
				}
	
				$this->read();
				$finished = 0;
			}
			else if($this->nodeType == XMLReader::END_ELEMENT)
			{
				if($this->localName == $nodeName)
					$finished = 1;
			}
		}
	}
	
	/**
	 * This function feeds from an XML file and converts it into an associative array
	 * 
	 * @param String $xmlPath	- the full path of the XML file
	 * 
	 * @throws Exception
	 * @return Array
	 */
	public function readXML($xmlPath)
	{
		$this->resetXMLArray();			/// reset the internal Array
		
		$this->setXMLPath($xmlPath);	/// Validates the XML Full path + Set the XML Path 
		
		/// start reading the file ///
		if($this->open($this->_xmlFullPath))
		{
			$this->read();
			
			if($this->nodeType == XMLReader::ELEMENT)
			{
				$this->_xmlArray[$this->localName] = array();
				$this->_xmlArray[$this->localName][0]['value'] = array();
				$this->_xmlArray[$this->localName][0]['attributes'] = array();
				
				if($this->hasAttributes)
				{
					while($this->moveToNextAttribute())
						$this->_xmlArray[$this->localName][0]['attributes'][$this->name] = trim($this->value);
				}
				
				$rootElement = $this->localName;
				
				$this->read();
				$this->readUntilNodeFinish($rootElement, $this->_xmlArray[$rootElement][0]);
			}
		}	
		else
			throw new Exception('Cannot read XML File');
		
		return $this->_xmlArray;
	}		
}