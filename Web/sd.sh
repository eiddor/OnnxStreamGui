echo "another attempt" >> /var/www/html/blah.txt

if test -f "/var/www/html/sd/.lock"; then
	echo "processing is still running." >> /var/www/html/output.txt
else
	echo "starting" > "/var/www/html/sd/.lock";
## Output some output :-)
	echo "output: /var/www/html/sd/$1.png";
	echo "pos-prompt: $2";
	echo "neg-prompt: $3";
	echo "steps: $4";
	echo "script path $5";
	echo "models-path: $6";
	
## Write our command to command.txt for debugging
	echo "$5 ./sd $7 --models-path $6 --output \"/var/www/html/sd/$1\" --prompt \"$2\" --neg-prompt \"$3\" --steps $4 --rpi" >> "/var/www/html/sd/command.txt"

	cd /

	cd $5

    ./sd $7 --models-path $6 --output "/var/www/html/sd/$1" --prompt "$2" --neg-prompt "$3" --steps $4 --rpi

	rm "/var/www/html/sd/.lock";
	echo "Done"
fi

