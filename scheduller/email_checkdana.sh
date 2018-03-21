export ORACLE_BASE=/usr/lib/oracle/xe/app/oracle
export ORACLE_HOME=$ORACLE_BASE/product/10.2.0/server
export TNS_ADMIN=$ORACLE_BASE/product/10.2.0/server/network/admin
export PATH=$ORACLE_HOME/bin:$PATH
export LD_LIBRARY_PATH=$ORACLE_HOME/lib:/lib:/usr/lib:$LD_LIBRARY_PATH
export CLASSPATH=$ORACLE_HOME/JRE:$ORACLE_HOME/jlib:$ORACLE_HOME/rdbms/jlib
cd /opt/lampp/htdocs/TaxSCB/RTE/scheduller/
/opt/lampp/bin/php /opt/lampp/htdocs/TaxSCB/RTE/scheduller/email_checkdana.php >> /opt/lampp/htdocs/TaxSCB/RTE/scheduller/email_checkdana.log
exit
