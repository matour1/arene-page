@extends('errors.layout', ['icone' => 'zap'])

@section('code', '429')
@section('titre', "Trop de requêtes")
@section('message', "Tu as effectué beaucoup de demandes en peu de temps. Patiente quelques instants puis réessaie.")
