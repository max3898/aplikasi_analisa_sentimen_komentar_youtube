<?php
    $start = microtime(true);
    /**
     * mysql> create database naiveBayes;
     * mysql> use naiveBayes;
     * mysql> create table trainingSet (S_NO integer primary key auto_increment, document text, category varchar(255));
     * mysql> create table wordFrequency (S_NO integer primary key auto_increment, word varchar(255), count integer, category varchar(255));
     */

    require_once('NaiveBayesClassifier.php');

    $classifier = new NaiveBayesClassifier();
    $spam = Category::$Positif;
    $ham = Category::$Negatif;

    $category = $classifier -> classify('mainya keren');
    echo 'mainya keren = '.$category."<br>";
    
    $category = $classifier -> classify('I knew it the prophecy Pew News has returned . ');
    echo 'I knew it the prophecy Pew News has returned .  = '.$category."<br>";

    $category = $classifier -> classify('Finally no censoring');
    echo 'Finally no censoring  = '.$category."<br>";

    $category = $classifier -> classify('Karya yang menarik buat orang terkagum');
    echo 'Karya yang menarik buat orang terkagum = '.$category."<br>";

    // $time_elapsed_secs = microtime(true) - $start;
    // echo "time execution = "$time_elapsed_secs." seconds";
?>