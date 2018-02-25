# Assistant

Simple GitHub Issue Bot.  
Who listens to creating a new issue in the repositories and thanks the user for that.


### Installing


```
composer create-project orchid/assistant [my-app-name]
```

Fill `.env` file and run:

```
php assistant.php
```

### Cron

add cron job:
```
*/15 * * * * php /path/to/command assistant.php
```


## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details