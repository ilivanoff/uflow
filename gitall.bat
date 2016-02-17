@echo off

git submodule init
git submodule update
git submodule foreach git pull origin master

rem Если передан какой-либо параметр, то не ожидаем
if "%1"=="" (
@pause
)
