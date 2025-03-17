
# FancyDumpVar - Elegant Debugging for PHP Variables

FancyDumpVar is a utility class designed for elegant and efficient variable output, ideal for debugging during module development. It supports flexible output formatting and provides an organized, visual representation of variables, including objects and arrays. It also includes options for recursion depth, element limits, and more.

## Features

- Elegant output for variables including arrays, objects, and other types
- Recursive handling of object references to avoid infinite loops
- Customizable options for sorting, overwriting stack variables, and displaying time information
- Built-in support for limiting recursion depth and the number of elements per level
- Easy-to-use API for dumping and outputting variables in a structured format

## Version

- Version: 2.5.6
- Release Date: 2025-03-14
- Author: Johannes Teitge (contact: johannes@teitge.de)

## License

This project is licensed under the **GPL-3.0-or-later** License - see the [LICENSE](LICENSE) file for details.

## Installation

To use FancyDumpVar in your project, you can download it directly from GitHub.

### Manual Installation:

1. **Download the Plugin:**
   - You can download the latest release of FancyDumpVar directly from [GitHub Releases](https://github.com/teitge/fancy-dumpvar/releases).
   
2. **Include the Plugin in Your Project:**
   - After downloading, extract the files and include the `FancyDumpVar.php` class file in your project. For example:

   ```php
   require_once '/path/to/FancyDumpVar.php';
   ```

3. **Using the Plugin:**
   - Once included, you can instantiate the class and use it to dump and format variables:

   ```php
   use FancyDumpVar\FancyDumpVar;

   // Initialize the class with sorting and time info enabled
   $dump = new FancyDumpVar();

   // Dump variables
   $dump->dump($var1, $var2);

   // Output the dumped variables
   $dump->dumpOut();
   ```

## Usage

Once installed, you can use FancyDumpVar in your project by calling the `dump()` and `dumpOut()` methods.

### Example Usage:

```php
use FancyDumpVar\FancyDumpVar;

// Initialize the class with sorting and time info enabled
$dump = new FancyDumpVar();

// Dump variables
$dump->dump($var1, $var2);

// Output the dumped variables
$dump->dumpOut();
```

### Methods

- `dump(...$vars)`: Dumps one or more variables to the stack.
- `dumpOut(...$selectedVars)`: Outputs the dumped variables in a formatted manner.

### Example Dump Output:

When using `dumpOut()`, the output will be neatly formatted and allow expanding or collapsing sections of the dumped data. Each dump can include variable names, values, type information, and time stamps.

## Configuration

You can customize the following options:

- **Sort properties and methods**: Sorts the properties and methods of objects.
- **Overwrite existing variables**: Allows overwriting previously dumped variables if they have the same name.
- **Show time information**: Displays the time when the variable was dumped.
- **Max depth**: Sets the maximum recursion depth for nested structures.

## Debugging & Error Handling

The plugin provides detailed error messages when issues occur during the dumping or output process. If a variable is not found, an error will be logged.

## Contributing

Feel free to fork this project and submit pull requests. If you have suggestions or encounter issues, please create an issue in the GitHub repository.

## Support

For any questions, issues, or feature requests, please contact the author at [johannes@teitge.de](mailto:johannes@teitge.de).

---

*FancyDumpVar is a tool designed with developers in mind to help with debugging and inspecting PHP variables in a clean and structured way.*
