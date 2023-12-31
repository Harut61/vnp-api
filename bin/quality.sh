#!/bin/bash

echo -e "\n\e[104m Qualitify \e[0m\n"

echo -e "\n\e[94m Starting... \e[0m\n"

array=( squizlabs/php_codesniffer phpmd/phpmd phpstan/phpstan symfony/phpunit-bridge )
for i in "${array[@]}"
do
	composer show | grep "${i}"
	if [[ $? -eq 0 ]]
    then
        echo -e "\n\e[30;48;5;82m ${i} ready \e[0m\n"
    else
        composer require --dev "${i}"
    fi
done
echo -e "------------------------------------------------------------------------------------------------------------\n"

echo -e "\e[94m PHP CS FIX \e[0m"
vendor/bin/php-cs-fixer fix src/
vendor/bin/php-cs-fixer fix config/

if [[ $? -eq 0 ]]
then
  echo -e "\n\e[30;48;5;82m Success! \e[0m\n"
fi
echo -e "------------------------------------------------------------------------------------------------------------\n"


echo -e "------------------------------------------------------------------------------------------------------------\n"

echo -e "\e[94m PHP Code Sniffer \e[0m"
vendor/bin/phpcs src/ tests/

if [[ $? -eq 0 ]]
then
  echo -e "\n\e[30;48;5;82m Success! \e[0m\n"
fi
echo -e "------------------------------------------------------------------------------------------------------------\n"

echo -e "\e[94m PHP Mess Detector \e[0m"
vendor/bin/phpmd src/ text cleancode

if [[ $? -eq 0 ]]
then
  echo -e "\n\e[30;48;5;82m Success! \e[0m\n"
fi
#echo -e "------------------------------------------------------------------------------------------------------------\n"

#echo -e "\e[94m PHP Stan \e[0m"
#vendor/bin/phpstan analyse -c phpstan.neon

#if [[ $? -eq 0 ]]
#then
#  echo -e "\n\e[30;48;5;82m Success! \e[0m\n"
#fi
#echo -e "------------------------------------------------------------------------------------------------------------\n"

echo -e "\e[94m Behat \e[0m"
vendor/bin/composer run behat

if [[ $? -eq 0 ]]
then
  echo -e "\n\e[30;48;5;82m Success! \e[0m\n"
fi
echo -e "------------------------------------------------------------------------------------------------------------\n"