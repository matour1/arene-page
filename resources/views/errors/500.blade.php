@extends('errors.layout', ['icone' => 'alert'])

@section('code', '500')
@section('titre', "Erreur serveur")
@section('message', "Quelque chose s'est mal passé de notre côté. On regarde, réessaie dans quelques minutes.")
