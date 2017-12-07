<?php

require_once __DIR__ . '/vendor/autoload.php';

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\GraphQL;
use GraphQL\Type\Definition\ResolveInfo;


try {
    

    $enderecoType = new ObjectType([
        'name' => 'Endereco',
        'fields' => [
            'cep' => [ 'type' => Type::string() ],
            'logradouro' => [ 'type' => Type::string() ],
            'complemento' => [ 'type' => Type::string() ],
            'bairro' => [ 'type' => Type::string() ],
            'localidade' => [ 'type' => Type::string() ],
            'uf' => [ 'type' => Type::string() ],            
        ]

    ]);


    $queryType = new ObjectType([
        'name' => 'Query',
        'fields' => [
            'endereco' => [
                'type' => $enderecoType,
                'args' => [                    
                    'cep' => ['type' => Type::nonnull(Type::string())],
                ],
                'resolve' => function ($root, $args) {
                    $cep = $args['cep'];

                    $filecache = "cache/{$cep}";
                    if (file_exists($filecache)) {
                        $result = file_get_contents($filecache);
                    } else {
                        $result = @file_get_contents("https://viacep.com.br/ws/{$cep}/json/");
                        if (!$result) {
                            return null;
                        }

                        file_put_contents($filecache, $result);
                    }

                    

                    return json_decode($result, true);
                }
            ],
        ]

    ]);

   
    $schema = new Schema([
        'query' => $queryType,        
    ]);

    $rawInput = '{"query":"{e1: endereco(cep: \"07083150\") {logradouro localidade bairro uf} e2: endereco(cep: \"07181100\") {localidade}}","variables":null,"operationName":null}';
    $input = json_decode($rawInput, true);

    $query = $input['query'];
    $variableValues = isset($input['variables']) ? $input['variables'] : null;
    $rootValue = [];

    $result = GraphQL::executeQuery($schema, $query, $rootValue, null, $variableValues);
    $output = $result->toArray();

} catch (\Exception $e) {
    
    $output = [
        'error' => [
            'message' => $e->getMessage()
        ]
    ];

}


print_r($output);