# Data Tools for EspoCRM

Tools that help you to get data information or fix data before import.

## 1. Skipping invalid email addresses for .csv

This tool helps to find invalid email addresses and skip them in the .csv file.
After running this tool will be created two .csv files:

- the first will contain the list of correct records.
- the second will contain the list of invalid records.

### Example of input data

| First name | Last name |  ... | Email Address | Work Email |
|:----:|:----:|:----:| :----:| :----:|
| Joe |  Coyle | .... | joe@tester.com | joe@workemail.com |
| Paul | Jones | .... | paul@tester | paul@workemail.com |
| Tom |  Richmond | .... | tom@tester.com | tom |
| Connie |  Patterson | .... | connie@tester.com | connie@workemail.com  |

### Result

#### Valid list

| First name | Last name |  ... | Email Address | Work Email |
|:----:|:----:|:----:| :----:| :----:|
| Joe |  Coyle | .... | joe@tester.com | joe@workemail.com |
| Connie |  Patterson | .... | connie@tester.com | connie@workemail.com  |

#### Invalid list

| First name | Last name |  ... | Email Address | Work Email |
|:----:|:----:|:----:| :----:| :----:|
| Paul | Jones | .... | paul@tester | paul@workemail.com |
| Tom |  Richmond | .... | tom@tester.com | tom |

### Usage

```
php command.php data-tool skipInvalidEmails --src="data/contacts.csv" --dest="data/valid.csv" --invalidDest="data/invalid.csv" --cells="Email Address, Work Email" --delimiter=","
```

- `src`: Input .csv file, e.g. "data/src.csv".
- `dest`: Valid list saved to .csv file, e.g. "data/valid.csv".
- `invalidDest`: Invalid list saved to .csv file, e.g. "data/invalid.csv".
- `delimiter`: Delimiter of your .csv file.
- `cells`: List of cells wich data should be fixed, e.g. "Email Address, Work Email".

## 2. Fixing multiple data in one cell for .csv

This tool help to fix data when one cell contains multiple data.

### Example of input data

| First name | Last name |  ... | Email Address | Pnone Number |
|:----:|:----:|:----:| :----:| :----:|
| Test |  Tester | .... | email1@tester.com, email2@tester.com, email3@tester.com | 089 123 123 12, 075 111 222 33 |

### Result

| First name | Last name |  ... | Email Address | Email Address 2 | Email Address 3 | Pnone Number | Pnone Number 2 |
|:----:|:----:|:----:| :----:| :----:|:----:|:----:|:----:|
| Test |  Tester | .... | email1@tester.com | email2@tester.com | email3@tester.com | 089 123 123 12 | 075 111 222 33 |

### Usage

```
php command.php data-tool fixCellData --src="data/contacts.csv" --dest="data/converted.csv" --cells="Work Email, Mobile Phone" --delimiter=";" --delimiterInsideCell=","
```

- `src`: Input .csv file, e.g. "data/src.csv".
- `dest`: Output .csv file, e.g. "data/dest.csv".
- `delimiter`: Delimiter of your .csv file.
- `delimiterInsideCell`: Delimiter inside a cell, e.g. "," for the data "email1@sample.com, email2@sample.com".
- `cells`: List of cells wich data should be fixed, e.g. "Phone Number, Work Email".

## 3. Get a column list of deleted fields

In case of deleting a field from Administration > Entity Manager, the field still exists in the database.

### Usage

```
php command.php data-tool deletedColums
```

- `dest`: Output .csv file, e.g. `--dest="data/list.csv"`.
- `delimiter`: Delimiter of generated .csv file, e.g. `--delimiter=","`.
- `entityTypes`: List of checked entities, e.g. `--entityTypes="Account, Contact"`.

## License

Change a license in `LICENSE` file. The current license is intended for scripts of this repository.
