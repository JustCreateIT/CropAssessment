<?php

class ExportToCSV {
	
	private $columnCount;
	private $csv_file;
	private $headerRow;
	
	function __construct() {
		// initialise
		$this->headerRow = false;
		$this->columnCount = 1;
		$this->csv_file = '';
	}
	
	/*
	function __destruct() {
		// cleaning service
		clear_object($this);
	}
	*/
	public function CSV_open($filename){
		
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename='.$filename);
		
		// create a file pointer connected to the output stream
		$this->csv_file = fopen('php://output', 'w');
	}	
	
	public function CSV_exit(){
		// release file pointer handle
		fclose($this->csv_file);
	}
	
	public function CSV_writeHeader($csv_header){
		
		if(isset($csv_header) && !empty($csv_header)){
			$this->headerRow = true;
			// save header column count
			$this->columnCount = sizeof($csv_header);
			// write out the column headings
			fputcsv($this->csv_file, $csv_header);
		}
	}
	
	public function CSV_writeRow($row){
		
		/*
		echo '<pre>';
			print_r('in csv_writeRow</br>');
			print_r($row);
			print_r(sizeof($row));
		echo '</pre>';
		*/
		// check row column count matches header column count
		if(self::checkColumnCount(sizeof($row))){
			// write out the data row
			fputcsv($this->csv_file, $row);		
		} else {
			// throw error
		}
	}
	
	private function checkColumnCount($i){
		if ($i === $this->columnCount) {
			return true;
		}
		return false;
	}
}
