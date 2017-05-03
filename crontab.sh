#!/bin/sh
if ps -ef | grep -v grep | grep srv.php ; then
        exit 0
else
        /opt/php70-regru/bin/php /root/bones/srv.php
        exit 0
fi