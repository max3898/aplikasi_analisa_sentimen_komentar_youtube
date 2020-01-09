<?php

    /**
     * @author Varun Kumar <varunon9@gmail.com>
     */
    require_once('Category.php');


    class NaiveBayesClassifier {

        public function __construct() {
        }

        /**
         * sentence is text(document) which will be classified as Negatif or Positif
         * @return category- Negatif/Positif
         */
        public function classify($sentence) {

            // extracting keywords from input text/sentence
            $keywordsArray = $this -> tokenize($sentence);

            // classifying the category
            $category = $this -> decide($keywordsArray);

            return $category;
        }

        public function precision() {

            require('db_connect.php');
            $sql = mysqli_query($conn, "select * from trainingset_stemmed where category = 'Positif'");
            $TP = 0;
            $FP = 0;

            while ($row = mysqli_fetch_array($sql)) {
                $classified = $this -> classify($row['teks']);
                if ($classified == "Positif") {
                    $TP++;
                }
                else{
                    $FP++;
                }
            }

            $precision = $TP/($TP+$FP);
            return $precision;
        }

        public function recall() {

            require('db_connect.php');
            $sql = mysqli_query($conn, "select * from trainingset_stemmed where category = 'Positif'");
            $TP = 0;
            $FN = 0;

            while ($row = mysqli_fetch_array($sql)) {
                $classified = $this -> classify($row['teks']);
                if ($classified == "Positif") {
                    $TP++;
                }
            }

            $sql = mysqli_query($conn, "select * from trainingset_stemmed where category = 'Negatif'");

            while ($row = mysqli_fetch_array($sql)) {
                $classified = $this -> classify($row['teks']);
                if ($classified == "Positif") {
                    $FN++;
                }
            }

            $recall = $TP/($TP+$FN);
            return $recall;
        }

        public function accuracy() {

            require('db_connect.php');
            
            $TP = 0;
            $TN = 0;
            $countTeks = 0;

            $sql = mysqli_query($conn, "select * from trainingset_stemmed where category = 'Positif'");
            while ($row = mysqli_fetch_array($sql)) {
                $classified = $this -> classify($row['teks']);
                if ($classified == "Positif") {
                    $TP++;
                }
                $countTeks++;
            }

            $sql = mysqli_query($conn, "select * from trainingset_stemmed where category = 'Negatif'");
            while ($row = mysqli_fetch_array($sql)) {
                $classified = $this -> classify($row['teks']);
                if ($classified == "Negatif") {
                    $TN++;
                }
                $countTeks++;
            }

            $sql = mysqli_query($conn, "select * from trainingset_stemmed where category = 'Bukan Bahasa Indonesia'");
            while ($row = mysqli_fetch_array($sql)) {
                $classified = $this -> classify($row['teks']);
                if ($classified == "Bukan Bahasa Indonesia") {
                    $TN++;
                }
                $countTeks++;
            }

            $accuracy = ($TP+$TN)/$countTeks;
            return $accuracy;
        }

        public function fscore($precision, $recall) {
            return (2*(($precision*$recall)/($precision+$recall)));
        }

        /**
         * @sentence- text/document provided by user as training data
         * @category- category of sentence
         * this function will save sentence aka text/document in trainingset_stemmed table with given category
         * It will also update count of words (or insert new) in penelitianwordfrequency table
         */
        public function train($sentence, $category) {
            $Positif = Category::$Positif;
            $Negatif = Category::$Negatif;
            $bukanBahasaIndonesia = Category::$bukanBahasaIndonesia;

            if ($category == $Positif || $category == $Negatif || $category == $bukanBahasaIndonesia) {
            
                //connecting to database
                require 'db_connect.php';

                // inserting sentence into trainingset_stemmed with given category
                // $sql = mysqli_query($conn, "INSERT into trainingset_stemmed (document, category) values('$sentence', '$category')");

                // extracting keywords
                $keywordsArray = $this -> tokenize($sentence);

                // updating penelitianwordfrequency table
                foreach ($keywordsArray as $word) {

                    // if this word is already present with given category then update count else insert
                    $sql = mysqli_query($conn, "SELECT count(*) as total FROM penelitianwordfrequency WHERE word = '$word' and category= '$category' ");
                    $count = mysqli_fetch_assoc($sql);

                    if ($count['total'] == 0) {
                        $sql = mysqli_query($conn, "INSERT into penelitianwordfrequency (word, category, count) values('$word', '$category', 1)");
                    } else {
                        $sql = mysqli_query($conn, "UPDATE penelitianwordfrequency set count = count + 1 where word = '$word'");
                    }
                }

                //closing connection
                $conn -> close();

            } else {
                throw new Exception('Unknown category. Valid categories are: $Negatif, $Positif, $bukanBahasaIndonesia');
            }
        }

        /**
         * this function takes a paragraph of text as input and returns an array of keywords.
         */

        private function tokenize($sentence) {
            require_once __DIR__ . '/vendor/autoload.php';

            $tokenizerFactory  = new \Sastrawi\Tokenizer\TokenizerFactory();
            $tokenizer = $tokenizerFactory->createDefaultTokenizer();
            
            $raw_comment = preg_replace('/\s+/', ' ', $sentence);
            $comment = preg_replace("/[^A-Za-z\  ]/", "", $raw_comment);
            $sentence = explode(" ",strtolower( $comment ));
            
            // $stopWords = array('about','and','are','com','for','from','how','that','the','this', 'was','what','when','where','who','will','with','und','the','www','yang', 'untuk', 'pada', 'ke', 'para', 'namun', 'menurut', 'antara', 'dia', 'dua','ia', 'seperti', 'jika', 'jika', 'sehingga', 'kembali', 'dan', 'tidak', 'ini', 'karena','kepada', 'oleh', 'saat', 'harus', 'sementara', 'setelah', 'belum', 'kami', 'sekitar','bagi', 'serta', 'di', 'dari', 'telah', 'sebagai', 'masih', 'hal', 'ketika', 'adalah','itu', 'dalam', 'bisa', 'bahwa', 'atau', 'hanya', 'kita', 'dengan', 'akan', 'juga','ada', 'mereka', 'sudah', 'saya', 'terhadap', 'secara', 'agar', 'lain', 'anda','begitu', 'mengapa', 'kenapa', 'yaitu', 'yakni', 'daripada', 'itulah', 'lagi', 'maka','tentang', 'demi', 'dimana', 'kemana', 'pula', 'sambil', 'sebelum', 'sesudah', 'supaya','guna', 'kah', 'pun', 'sampai', 'sedangkan', 'selagi', 'sementara', 'tetapi', 'apakah','kecuali', 'sebab', 'selain', 'seolah', 'seraya', 'seterusnya', 'tanpa', 'agak', 'boleh','dapat', 'dsb', 'dst', 'dll', 'dahulu', 'dulunya', 'anu', 'demikian', 'tapi', 'ingin','juga', 'nggak', 'mari', 'nanti', 'melainkan', 'oh', 'ok', 'seharusnya', 'sebetulnya','setiap', 'setidaknya', 'sesuatu', 'pasti', 'saja', 'toh', 'ya', 'walau', 'tolong','tentu', 'amat', 'apalagi', 'bagaimanapun');
            $stopWords = array();

            //an empty array
            $keywordsArray = array();

            foreach ($sentence as $word){ 
              //excluding elements of length less than 3
                if (!(strlen($word) <= 1)) {

                    //excluding elements which are present in stopWords array
                    //http://www.w3schools.com/php/func_array_in_array.asp
                    if (!(in_array($word, $stopWords))) {
                        array_push($keywordsArray, $word);
                    }
                } 
            } 
            return $keywordsArray;
        }

        /**
         * This function takes an array of words as input and return category (Positif/Negatif) after
         * applying Naive Bayes Classifier
         *
         * Naive Bayes Classifier Algorithm -
         *
         *   p(Positif/bodyText) = p(Positif) * p(bodyText/Positif) / p(bodyText);
         *   p(Negatif/bodyText) = p(Negatif) * p(bodyText/Negatif) / p(bodyText);
         *   p(bodyText) is constant so it can be ommitted
         *   p(Positif) = no of documents (sentence) belonging to category Positif / total no of documents (sentence)
         *   p(bodyText/Positif) = p(word1/Positif) * p(word2/Positif) * .... p(wordn/Positif)
         *   Laplace smoothing for such cases is usually given by (c+1)/(N+V), 
         *   where V is the vocabulary size (total no of different words)
         *   p(word/Positif) = no of times word occur in Positif / no of all words in Positif
         *   Reference:
         *   http://stackoverflow.com/questions/9996327/using-a-naive-bayes-classifier-to-classify-tweets-some-problems
         *   https://github.com/ttezel/bayes/blob/master/lib/naive_bayes.js
        */
        private function decide ($keywordsArray) {
            $Positif = Category::$Positif;
            $Negatif = Category::$Negatif;
            $bukanBahasaIndonesia = Category::$bukanBahasaIndonesia;

            // by default assuming category to be Negatif
            $category = $Negatif;

            // making connection to database
            require 'db_connect.php';

            $sql = mysqli_query($conn, 'SELECT count(*) as total FROM `penelitianwordfrequency` WHERE category = "Positif" ');
            $PositifCount = mysqli_fetch_assoc($sql);
            $PositifCount = $PositifCount['total'];

            $sql = mysqli_query($conn, 'SELECT count(*) as total FROM `penelitianwordfrequency` WHERE category = "Negatif" ');
            $NegatifCount = mysqli_fetch_assoc($sql);
            $NegatifCount = $NegatifCount['total'];

            $sql = mysqli_query($conn, 'SELECT count(*) as total FROM `penelitianwordfrequency` WHERE category = "Bukan Bahasa Indonesia" ');
            $bukanBahasaIndonesiaCount = mysqli_fetch_assoc($sql);
            $bukanBahasaIndonesiaCount = $bukanBahasaIndonesiaCount['total'];

            $sql = mysqli_query($conn, "SELECT count(*) as total FROM penelitianwordfrequency ");
            $totalCount = mysqli_fetch_assoc($sql);
            $totalCount = $totalCount['total'];

            //p(Positif)
            $pPositif = $PositifCount / $totalCount; 
            // (no of documents classified as Positif / total no of documents)

            //p(Negatif)
            $pNegatif = $NegatifCount / $totalCount; // (no of documents classified as Negatif / total no of documents)

            //p(bukanBahasaIndonesia)
            $pbukanBahasaIndonesia = $bukanBahasaIndonesiaCount / $totalCount; // (no of documents classified as Negatif / total no of documents)
            //echo $pPositif." ".$pNegatif;
            
            // no of distinct words (used for laplace smoothing)
            $sql = mysqli_query($conn, "SELECT count(*) as total FROM penelitianwordfrequency");
            $distinctWords = mysqli_fetch_assoc($sql);
            $distinctWords = $distinctWords['total'];

            $bodyTextIsPositif = $pPositif;
            foreach ($keywordsArray as $word) {
                $sql = mysqli_query($conn, "SELECT * FROM penelitianwordfrequency where word = '$word' and category = 'Positif' ");
                $wordCount = mysqli_fetch_assoc($sql);
                $wordCount = $wordCount['count'];
                if ($wordCount == NULL) {
                    $wordCount = 0;
                }
                $bodyTextIsPositif *= $wordCount + 1 / abs($PositifCount + $distinctWords);
            }

            $bodyTextIsNegatif = $pNegatif;
            foreach ($keywordsArray as $word) {
                $sql = mysqli_query($conn, "SELECT * FROM penelitianwordfrequency where word = '$word' and category = 'Negatif' ");
                $wordCount = mysqli_fetch_assoc($sql);
                $wordCount = $wordCount['count'];
                if ($wordCount == NULL) {
                    $wordCount = 0;
                }
                $bodyTextIsNegatif *= $wordCount + 1 / abs($NegatifCount + $distinctWords);
            }

            $bodyTextIsBukanBahasaIndonesia = $pbukanBahasaIndonesia;
            foreach ($keywordsArray as $word) {
                $sql = mysqli_query($conn, "SELECT * FROM penelitianwordfrequency where word = '$word' and category = 'Bukan Bahasa Indonesia' ");
                $wordCount = mysqli_fetch_assoc($sql);
                $wordCount = $wordCount['count'];
                if ($wordCount == NULL) {
                    $wordCount = 0;
                }
                $bodyTextIsBukanBahasaIndonesia *= $wordCount + 1 / abs($bukanBahasaIndonesiaCount + $distinctWords);
            }

            if ($bodyTextIsNegatif >= $bodyTextIsPositif && $bodyTextIsNegatif >= $bodyTextIsBukanBahasaIndonesia) {
                $category = $Negatif;
            } else if($bodyTextIsBukanBahasaIndonesia >= $bodyTextIsPositif){
                $category = $bukanBahasaIndonesia;
            }
            else{
                $category = $Positif;
            }

            $conn -> close();

            return $category;
        }
    }

?>
