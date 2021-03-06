<?php

include 'debug.class.php';

/**
 * @debug allow to show (or not) debug_messages
 */
class csvWorker extends debug{

  var $input_folder;
  var $output_folder;
  var $current_file;
  var $files;

  public function csvWorker($input = 'input', $output = 'output'){
    parent::__construct();
    $this->input_folder = $input;
    $this->output_folder = $output;
    $this->files = array();
    $this->current_file = '';
  }

  public function test(){

    if (file_exists($this->input_folder) && file_exists($this->output_folder)){
      $this->debug_message('Class declaration was ok.');
    }else{
      $this->debug_message('Input or output folder don\'t exist or haven\'n permissions to edit it');
    }
  }

  public function init(){
    $this->files = scandir($this->input_folder);
    unset($this->files[0]);
    unset($this->files[1]);
  }

  public function work(){

    //foreach file in $input_folder
    while(($this->current_file = array_pop($this->files)) != ''){

      $input_stream = fopen($this->input_folder . '/' . $this->current_file, "r");
      $this->debug_message('Processing ' . $this->input_folder . '/'  . $this->current_file);
      //create output file with the same name. Ir it already exist first it be truncated.
      if (($output_stream = fopen($this->output_folder . '/' . $this->current_file, "w")) === FALSE){
        $this->debug_message('Can\'t create an output file', self::ERROR);
        break;
      };
      
      $this->debug_message('Created ' . $this->output_folder . '/' . $this->current_file);

      //Ignore first line (Header) and test read
      if(fgetcsv($input_stream) === FALSE){
        $this->debug_message('Can\'t read input file.');
      };

      //Read whole file line per line (becouse it can be too large)
      $buffer_fields = array();
      while(($line = fgetcsv($input_stream)) !== FALSE){

        $write = true;
        if(preg_match('/\[summary\'/', $line[0])){
          $line = '';
          $write = false;
        }

        if($write){
          //HTML labels
          $line[1] = preg_replace('/<( )*(\/)?(p|ul|li|br|strong|em|BR|STRONG|P)( )*(\/)?>/', '', $line[1]); 
          //HTML special chars
          $line[1] = html_entity_decode($line[1]);
          //line breaks
          $line[1] = preg_replace('/( ){4,15}/', '
', $line[1]);

          //Reorder, always put first the title field
          if(preg_match('/\[title_field\]/', $line[0])){
            fputcsv($output_stream, $line);
            foreach($buffer_fields as $field){
              fputcsv($output_stream, $field);
            }
            $buffer_fields = array();
          }else{
            $buffer_fields[] = $line;
          }
        }
      }

      fclose($output_stream);
      fclose($input_stream);
    }
  }

}

?>
