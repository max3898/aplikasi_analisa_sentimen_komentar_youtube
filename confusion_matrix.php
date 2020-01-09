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

    $precision = $classifier -> precision();
    echo "Precision = ".$precision."<br>";

    $recall = $classifier -> recall();
    echo "Recall = ".$recall."<br>";

    $accuracy = $classifier -> accuracy();
    echo "accuracy = ".$accuracy."<br>";

    $fscore = $classifier -> fscore($precision, $recall);
    echo "fscore = ".$fscore."<br>";

    $time_elapsed_secs = microtime(true) - $start;
    echo "time execution = ".$time_elapsed_secs." seconds";
?>