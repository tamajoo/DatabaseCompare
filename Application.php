<?php 

namespace Tamajoo;

class Application
{

	public $databases = [];

	public function addDatabase($config)
	{

		$config = array_merge([
			'host'		=> '127.0.0.1',
			'db'		=> 'test',
			'user'		=> 'root',
			'pass'		=> '',
			'charset'	=> 'utf8mb4',
		], $config);

		$host 		= $config['host'];
		$db   		= $config['db'];
		$user 		= $config['user'];
		$pass 	 	= $config['pass'];
		$charset	= $config['charset'];

		$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
		
		$options = [
		    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		    PDO::ATTR_EMULATE_PREPARES   => false,
		];

		try {
		    
		    $databases[] = [
		    	'config' => $config,
		    	'pdo' => new PDO($dsn, $user, $pass, $options)
		    ];
		
		} catch (\PDOException $e) {
		    
		    throw new \PDOException($e->getMessage(), (int)$e->getCode());
		
		}
	}


	public function getSchema($database) 
	{	
		$name = $database['config']['db'];
		$pdo = $database['pdo'];
		
		$sql = "SELECT * FROM information_schema.columns WHERE table_schema = ?";
		$stmt = $pdo->prepare($sql);
		$stmt->execute([$name]);

		$schema = [];
		while ($row = $stmt->fetch()) {
			$row = array_change_key_case($row);

			$table = $row['table_name'];
			$column = $row['column_name'];

			$schema[$table] = @$schema[$table] ?: [];
			$schema[$table][$column] = [
				'type' => $row['column_type'],
				'nullable' => $row['is_nullable'] != 'NO',
				'default' => $row['column_default'],
				'key' => $row['column_key'],
			];
		}

		return $schema;
	}


	public function getSchemas() 
	{	
		$schemas = [];
		foreach ($this->databases as $k => $database) {
			$schemas[] = $this->getSchema($database);
		}
		return $schemas;
	}


	public function compare() 
	{
		$schemas = $this_>getSchemas();
	}

}