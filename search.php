<!DOCTYPE html>
<html>
    <head>
    </head>
    <body>
        <h1>Search for educational content</h1>
        <?php
            $file = __DIR__ . '.env';
            if (file_exists($file)) {
                $lines = file($file);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line) || strpos($line, '#') === 0) {
                        continue; // Skip empty lines and comments
                    }
                    list($key, $value) = explode('=', $line, 2);
                    // Set the environment variable using putenv
                    putenv(trim($key) . '=' . trim($value));
                }
            }
            $curl = curl_init();
            $queryz = $_GET['query'];
            echo getenv("BEARER");
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.themoviedb.org/3/search/movie?query=' . $query,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . getenv("BEARER")
                ),
            ));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response_data = curl_exec($curl);
            if (curl_errno($curl)) {
                $error_msg = curl_error($curl);
                echo "Error getting : " . $error_msg;
            }
            curl_close($curl);
            echo $response_data;
            $data = json_decode($response_data, true);
            $results = $data['results'] ?? [];
            foreach ($results as &$result) {
                unset($result['genre_ids']);
                $result['id'] = (string) $result['id']; 
                unset($result['original_title']);
                $result['year'] = substr($result['release_date'], 0, 4);
                unset($result['release_date']);
                unset($result['video']);
                unset($result['vote_average']);
                unset($result['vote_count']);
                $result['link'] = '/movieplayer/' . $result['id'];
                $result['description'] = $result['overview'];
                $result['poster'] = 'https://image.tmdb.org/t/p/w300_and_h450_bestv2/' . $result['poster_path'];
            }
            echo json_encode($results);        
        ?>
    </body>