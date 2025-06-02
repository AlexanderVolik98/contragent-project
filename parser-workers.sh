#!/bin/bash

for i in {1..2}
do
  php bin/console app:parser-from-files --path=./data/inn --workers-count=2 --worker-id=$i --chunk-size=5000 &
done

wait