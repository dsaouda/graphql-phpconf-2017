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
            'cep' => [ 
                'type' => Type::string(),
                'resolve' => function ($root, $args) {
                    return 'esse é o cep que você usou para busca => ' . $root['cep'];
                }
            ],
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


    $mutationType = new ObjectType([
        'name' => 'Cache',
        'fields' => [
            'removerCache' => [
                'type' => Type::boolean(),
                'args' => [
                    'cep' => [
                        'type' => Type::nonnull(Type::string()),                        
                        'description' => 'O cep que terá o cache removido'
                    ],

                ],
                'resolve' => function ($root, $args) {
                    $cep = $args['cep'];
                    
                    $filecache = "cache/{$cep}";                    
                    return unlink($filecache);
                },
            ],
        ],
    ]);

   
    $schema = new Schema([
        'query' => $queryType,
        'mutation' => $mutationType,
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