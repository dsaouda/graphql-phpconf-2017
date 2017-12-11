# graphql phpconf 2017

Exemplos simples usados para fazer uma pequena demonostração de graphql no phpconf 2017

# rodando

execute em seu terminal ```php -S 0.0.0.0:8080```

em um editor (você pode usar o [graphiql](https://github.com/graphql/graphiql)) coloque a url http://<host>/02-cep.php

# exemplos de querys

## query

```
{
  endereco(cep: "07083150") {
    logradouro 
    localidade 
    bairro 
    uf 
    cep
  }  
}
```

======================================

## variable

```
query ($cep: String!){
  endereco(cep: $cep) {
    logradouro 
    localidade 
    bairro 
    uf 
    cep
  }  
}

#query variables
{
  "cep": "07181100"
}
```

======================================
## alias

```
{
  e1: endereco(cep: "07083150") {
    logradouro 
    localidade 
    bairro 
    uf 
    cep
  }  
  
  e2: endereco(cep: "07181100") {
    localidade     
  }  
  
  e3: endereco(cep: "06020190") {
    logradouro
    uf
  }  
}

```
======================================

## fragments

```
{
  e1: endereco(cep: "07083150") {
    ...detalhe
  }  
  
  e2: endereco(cep: "07181100") {
    ...detalhe     
  }  
  
  e3: endereco(cep: "06020190") {
    ...detalhe
  }  
}

fragment detalhe on Endereco {
  logradouro
  localidade
  uf
}

```

======================================

## directives
```
query ($cep: String!, $includeBairro: Boolean = true){
  endereco(cep: $cep) {
    logradouro 
    localidade 
    bairro @include(if: $includeBairro)
    uf 
    cep
  }  
}

#query variables
{
  "cep": "07181100",
  "includeBairro": true
}
```

======================================

## mutation
```
mutation {
  removerCache(cep: "06020190")
}
```
