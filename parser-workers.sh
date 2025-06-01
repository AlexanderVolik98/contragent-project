#!/bin/bash

for i in {1..29}
do
  php bin/console app:parser-from-files --workers-count=30 --worker-id=$i --chunk-size=5000 &
done

wait