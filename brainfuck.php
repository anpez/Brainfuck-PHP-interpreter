<?php

/*************************************************************************
 Copyright 2012 Antonio Nicolás Pina

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

     http://www.apache.org/licenses/LICENSE-2.0

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
*************************************************************************/

class Brainfuck
{
	public $_instructions;
	public $_program;
	public $_dp;
	public $_ip;
	public $_mem;

	function __construct()
	{
		$this->_instructions = array
		(
			'>' => function($obj) { ++$obj->_dp; },
			'<' => function($obj) { --$obj->_dp; },
			'+' => function($obj) { ++$obj->_mem[$obj->_dp]; },
			'-' => function($obj) { --$obj->_mem[$obj->_dp]; },
			'.' => function($obj) { printf('%c', $obj->_mem[$obj->_dp]); },
			',' => function($obj) { fscanf(STDIN, '%c', $chr); $obj->_mem[$obj->_dp] = ord($chr); },
			'[' => function($obj)
			{
				if (!$obj->_mem[$obj->_dp])
				{
					$count = 1;
					while($count)
					{
						switch($obj->_program[++$obj->_ip])
						{
							case '[':
								++$count;
								break;
							case ']':
								--$count;
								break;
						}
					}
				}
			},
			']' => function($obj)
			{
				$count = 1;
				while($count)
				{
					switch($obj->_program[--$obj->_ip])
					{
						case ']':
							++$count;
							break;
						case '[':
							--$count;
							break;
					}
				}
				--$obj->_ip;
			}
		);
	}

	function interpret($file)
	{
		$this->_program = @file_get_contents($file);
		if (!$this->_program)
		{
			die('Error: couldn\'t read file "'.$file."\".\n");
		}

		$this->_dp = 0;
		$this->_ip = 0;
		$this->_mem = array_fill(0, 30000, 0);

		$psize = strlen($this->_program);
		for($this->_ip = 0; $this->_ip < $psize; ++$this->_ip)
		{
			if (!array_key_exists($this->_program[$this->_ip], $this->_instructions)) continue;
			$this->_instructions[$this->_program[$this->_ip]]($this);
		}
	}
}

if ($argc < 2)
{
	die('Usage: '.$argv[0]." file [file ...]\n");
}

$interpreter = new Brainfuck;
foreach(array_slice($argv, 1) as $file)
{
	$interpreter->interpret($file);
}

/* End of file brainfuck.php */
