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
            'cidade' => [ 'type' => Type::string() ],
            'uf' => [ 'type' => Type::string() ],            
        ]

    ]);


    $queryType = new ObjectType([
        'name' => 'Query',
        'fields' => [
            'endereco' => [
                'type' => $enderecoType,
                'args' => [                    
                    'cep' => ['type' => Type::string()],
                ],
                'resolve' => function ($root, $args) {
                    return [                        
                      'cep' => '01001-000',
                      'logradouro' => 'Praça da Sé',
                      'complemento' => 'lado ímpar',
                      'bairro' => 'Sé',
                      'localidade' => 'São Paulo'
                      'uf' => 'SP',
                      'unidade' => '',
                      'ibge' => '3550308',
                      'gia' => '1004'
                    ];
                }
            ],
        ]

    ]);

   
    $schema = new Schema([
        'query' => $queryType,        
    ]);

    $rawInput = file_get_contents('php://input');
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

header('Content-Type: application/json; charset=UTF-8');
echo json_encode($output);