# Import Tools for EspoCRM

Tools that help you fix data before import

## 1. Fixing multiple data in one cell

### Example of input data

| First name | Last name |  ... | Email Address | Pnone Number |
|:----:|:----:|:----:| :----:| :----:|
| Test |  Tester | .... | email1@tester.com, email2@tester.com, email3@tester.com | 089 123 123 12, 075 111 222 33 |

### Result

| First name | Last name |  ... | Email Address | Email Address 2 | Email Address 3 | Pnone Number | Pnone Number 2 |
|:----:|:----:|:----:| :----:| :----:|:----:|:----:|:----:|
| Test |  Tester | .... | email1@tester.com | email2@tester.com | email3@tester.com | 089 123 123 12 | 075 111 222 33 |

### Run

```
php command.php csvTool fixCellData --src="data/contacts.csv" --dest="data/converted.csv" --cells="Work Email, Mobile Phone" --delimiter="," --delimiterInsideCell=","
```

- `src`: Input .csv file, e.g. "data/src.csv".
- `dest`: Output .csv file, e.g. "data/dest.csv".
- `delimiter`: Delimiter of your .csv file.
- `delimiterInsideCell`: Delimiter inside a cell, e.g. "," for the data "email1@sample.com, email2@sample.com".
- `cells`: List of cells wich data should be fixed, e.g. "Phone Number, Work Email".

## License

Change a license in `LICENSE` file. The current license is intended for scripts of this repository.
