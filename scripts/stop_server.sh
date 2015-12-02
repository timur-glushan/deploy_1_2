#!/bin/bash
isExistApp=`pgrep apache`
if [[ -n  \$isExistApp ]]; then
   service apache2 stop
fi
