#Apache Rewriterule to enable thumb and download-streaming
RewriteRule ^/comvosfilelist/(.*)/(.*)/(.*) /index.php?id=$1&tx_comvosfilelist_pi1[action]=stream&tx_comvosfilelist_pi1[file]=$2&cHash=$3 [L]

