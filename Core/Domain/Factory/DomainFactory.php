<?php
	abstract class DomainFactory
	{
		  //
		 // CONSTRUCTOR
		//
		
		protected function __construct() {
		
		}
		
		  //
		 // METHODS
		//
		
		public abstract function FromDataRow(Domain $object, DatabaseRow $row);
	}
?>