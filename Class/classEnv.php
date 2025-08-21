<?php


class DotEnv
{
    /**
     * The directory where the .env file can be located.
     *
     * @var string
     */
    protected $path;


    public function __construct(string $path)
    {   
         
        if(!file_exists($path)) {
             throw new \InvalidArgumentException(sprintf('%s does not exist', $path));
        }
        $this->path = $path;
        $this->load();
    }

    private function load() :void
    {
        if (!is_readable($this->path)) {
             throw new \RuntimeException(sprintf('%s file is not readable', $this->path));
        }

        $lines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {

            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                    putenv(sprintf('%s=%s', $name, $value));
                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }
    }

    public function listVars(){
        // Debug: ver qué variables están disponibles
        error_log("Variables de entorno disponibles:");
        error_log("HOST_CENTRAL: " . (getenv('HOST_CENTRAL') ?: 'NO_DEFINIDA'));
        error_log("DATABASE_CENTRAL: " . (getenv('DATABASE_CENTRAL') ?: 'NO_DEFINIDA'));
        error_log("USER: " . (getenv('USER') ?: 'NO_DEFINIDA'));
        error_log("PASS: " . (getenv('PASS') ?: 'NO_DEFINIDA'));
        
        $vars = array(
            'HOST_CENTRAL' => getenv('HOST_CENTRAL') ?: $_ENV['HOST_CENTRAL'] ?? false,
            'HOST_LOCALES' => getenv('HOST_LOCALES') ?: $_ENV['HOST_LOCALES'] ?? false,
            'DATABASE_CENTRAL' => getenv('DATABASE_CENTRAL') ?: $_ENV['DATABASE_CENTRAL'] ?? false,
            'DATABASE_LOCALES' => getenv('DATABASE_LOCALES') ?: $_ENV['DATABASE_LOCALES'] ?? false,
            'USER' => getenv('USER') ?: $_ENV['USER'] ?? false,
            'PASS' => getenv('PASS') ?: $_ENV['PASS'] ?? false,
            'PASS_LOCALES' => getenv('PASS_LOCALES') ?: $_ENV['PASS_LOCALES'] ?? false,
            'CHARACTER' => getenv('CHARACTER') ?: $_ENV['CHARACTER'] ?? false,
            'HOST_MONGO' => getenv('HOST_MONGO') ?: $_ENV['HOST_MONGO'] ?? 'mongodb://localhost:27017',
            'DATABASE_MONGO' => getenv('DATABASE_MONGO') ?: $_ENV['DATABASE_MONGO'] ?? 'local',
            'DATABASE_UY' => getenv('DATABASE_UY') ?: $_ENV['DATABASE_UY'] ?? false,
            'ENV' => getenv('ENV') ?: $_ENV['ENV'] ?? false,
        );

        // Debug: ver qué variables se están retornando
        error_log("Variables retornadas: " . json_encode($vars));

        return $vars;
    }

}