# DropDownOptionsGenerator

DropDownOptionsGenerator is a standalone PHP class which works in any project or in any PHP frameworks. The Purpose of this class is to Generate HTML of Options for Drop Down. It is currently work on Data (Oject of Array which is Fetched From Database)


## Documentation#

##Data Source For Tutorial

	stdClass Object
	(
    [0] => stdClass Object
        (
            [id] => 1
            [title] => Harry Potter and the Philosopher's Stone
            [author] => J. K. Rowling
            [publisher] => Arthur A. Levine Books
            [wikipedia_link] => https://en.wikipedia.org/wiki/Harry_Potter_and_the_Philosopher
        )

    [1] => stdClass Object
        (
            [id] => 2
            [title] => Harry Potter and the Chamber of Secrets
            [author] => J. K. Rowling
            [publisher] => Arthur A. Levine Books
            [wikipedia_link] => https://en.wikipedia.org/wiki/Harry_Potter_and_the_Chamber_of_Secrets
        )

    [2] => stdClass Object
        (
            [id] => 2
            [title] => Harry Potter and the Prisoner of Azkaban
            [author] => J. K. Rowling
            [publisher] => Arthur A. Levine Books
            [wikipedia_link] => https://en.wikipedia.org/wiki/Harry_Potter_and_the_Prisoner_of_Azkaban
        )
	)

### Example 1: Minimun Config 

	$config = [
	    'option' => [
	        'label' => 'title',
	        'value' => 'id',
	    ]
	];
	$generator = new DropDownOptionsGenerator($config);
	$markput = $generator->generateMarkup($data);

	// Output
	// <option value=""  >Select</option>
	// <option value="1"  >Harry Potter and the Philosopher's Stone</option>
	// <option value="2"  >Harry Potter and the Chamber of Secrets</option>
	// <option value="2"  >Harry Potter and the Prisoner of Azkaban</option>

### Example 2: Default First Option 

	$config = [
	    'option' => [
	        'default' => [
	            'label' => 'Select Harry Potter Book',
	            'value' => ''
	        ],
	        'label' => 'title',
	        'value' => 'id',
	    ]
	];
	$generator = new DropDownOptionsGenerator($config);
	$markput = $generator->generateMarkup($data);

	// Output
	// <option value=""  >Select Harry Potter Book</option>
	// <option value="1"  >Harry Potter and the Philosopher's Stone</option>
	// <option value="2"  >Harry Potter and the Chamber of Secrets</option>
	// <option value="2"  >Harry Potter and the Prisoner of Azkaban</option>


### Example 3: Select Option By Default 

	$config = [
	    'option' => [
	        'default' => [
	            'label' => 'Select Harry Potter Book',
	            'value' => ''
	        ],
	        'label' => 'title',
	        'value' => 'id',
			'selected' => 2
	    ]
	];
	$generator = new DropDownOptionsGenerator($config);
	$markput = $generator->generateMarkup($data);

	// Output
	// <option value=""  >Select Harry Potter Book</option>
	// <option value="1"  >Harry Potter and the Philosopher's Stone</option>
	// <option value="2" selected="selected" >Harry Potter and the Chamber of Secrets</option>
	// <option value="2"  >Harry Potter and the Prisoner of Azkaban</option>

### Example 4: Select Option By Default 

	$config = [
	    'option' => [
	        'default' => [
	            'label' => 'Select Harry Potter Book',
	            'value' => ''
	        ],
	        'label' => 'title',
	        'value' => 'id',
	        'selected' => 2,
	        'data_attributes' => [
	            'author',
	            'publisher'
	        ]
	    ]
	];
	$generator = new DropDownOptionsGenerator($config);
	$markput = $generator->generateMarkup($data);
	
	// Output
	// <option value=""  >Select Harry Potter Book</option>
	// <option value="1"  data-author='J. K. Rowling' data-publisher='Arthur A. Levine Books' >Harry Potter and the Philosopher's Stone</option>
	// <option value="2" selected='selected' data-author='J. K. Rowling' data-publisher='Arthur A. Levine Books' >Harry Potter and the Chamber of Secrets</option>
	// <option value="3"  data-author='J. K. Rowling' data-publisher='Arthur A. Levine Books' >Harry Potter and the Prisoner of Azkaban</option>

### Example 5: Exclude or Build Condition To Filter Records 

	$config = [
	    'option' => [
	        'default' => [
	            'label' => 'Select Harry Potter Book',
	            'value' => ''
	        ],
	        'label' => 'title',
	        'value' => 'id',
	        'selected' => 2,
	        'data_attributes' => [
	            'author',
	            'publisher'
	        ],
	        'exclude' => [
	            'AND' => [
	                [
	                    'column_name' => 'id',
	                    'operator' => '==',
	                    'value' => 2
	                ],
	                'prefix' => '!' // Optional
	            ]
	        ]
	    ]
	];
	$generator = new DropDownOptionsGenerator($config);
	$markput = $generator->generateMarkup($data);
	
	// Output with Prefix
	// <option value=""  >Select Harry Potter Book</option>
	// <option value="1"  data-author='J. K. Rowling' data-publisher='Arthur A. Levine Books' >Harry Potter and the Philosopher's Stone</option>
	// <option value="3"  data-author='J. K. Rowling' data-publisher='Arthur A. Levine Books' >Harry Potter and the Prisoner of Azkaban</option>

	// Output without Prefix
	// <option value=""  >Select Harry Potter Book</option>
	// <option value="2" selected='selected' data-author='J. K. Rowling' data-publisher='Arthur A. Levine Books' >Harry Potter and the Chamber of Secrets</option>


### Example 6: Exclude or Build Condition with more than 1 column (composite) To Filter Records 

	$config = [
	    'option' => [
	        'default' => [
	            'label' => 'Select Harry Potter Book',
	            'value' => ''
	        ],
	        'label' => 'title',
	        'value' => 'id',
	        'selected' => 2,
	        'data_attributes' => [
	            'author',
	            'publisher'
	        ],
	        'exclude' => [
	            "AND" => [
	                'composite' => [
	                    [
	                        [
	                            'column_name' => 'id',
	                            'operator' => '==',
	                            'value' => 2
	                        ],
	                        [
	                            'column_name' => 'author',
	                            'operator' => '==',
	                            'value' => 'J. K. Rowling'
	                        ],
	                    ],
	                    'prefix' => '!'
	                ]
	            ]
	        ]
	    ]
	];
	$generator = new DropDownOptionsGenerator($config);
	$markput = $generator->generateMarkup($data);
	
	// Output with Prefix
	// <option value=""  >Select Harry Potter Book</option>
	// <option value="1"  data-author='J. K. Rowling' data-publisher='Arthur A. Levine Books' >Harry Potter and the Philosopher's Stone</option>
	// <option value="3"  data-author='J. K. Rowling' data-publisher='Arthur A. Levine Books' >Harry Potter and the Prisoner of Azkaban</option>

	// Output without Prefix
	// <option value=""  >Select Harry Potter Book</option>
	// <option value="2" selected='selected' data-author='J. K. Rowling' data-publisher='Arthur A. Levine Books' >Harry Potter and the Chamber of Secrets</option>

## Data Source For the Option Group

	stdClass Object
	(
    [0] => stdClass Object
        (
            [id] => 1
            [title] => Harry Potter and the Philosopher's Stone
            [author] => J. K. Rowling
            [publisher] => Arthur A. Levine Books
            [wikipedia_link] => https://en.wikipedia.org/wiki/Harry_Potter_and_the_Philosopher
            [childrens] => stdClass Object
                (
                    [0] => stdClass Object
                        (
                            [name] => Professor Albus Dumbledore
                            [orignal_name] => Richard Harris
                        )

                    [1] => stdClass Object
                        (
                            [name] => Professor Minerva McGonagall
                            [orignal_name] => Maggie Smith
                        )

                )

        )

    [1] => stdClass Object
        (
            [id] => 2
            [title] => Harry Potter and the Chamber of Secrets
            [author] => J. K. Rowling
            [publisher] => Arthur A. Levine Books
            [wikipedia_link] => https://en.wikipedia.org/wiki/Harry_Potter_and_the_Chamber_of_Secrets
            [childrens] => stdClass Object
                (
                    [0] => stdClass Object
                        (
                            [name] => Rubeus Hagrid
                            [orignal_name] => Robbie Coltrane
                        )

                    [1] => stdClass Object
                        (
                            [name] => Harry Potter
                            [orignal_name] => Daniel Radcliffe
                        )

                )

        )

    [2] => stdClass Object
        (
            [id] => 3
            [title] => Harry Potter and the Prisoner of Azkaban
            [author] => J. K. Rowling
            [publisher] => Arthur A. Levine Books
            [wikipedia_link] => https://en.wikipedia.org/wiki/Harry_Potter_and_the_Prisoner_of_Azkaban
            [childrens] => stdClass Object
                (
                    [0] => stdClass Object
                        (
                            [name] => Dudley Dursley
                            [orignal_name] => Harry Melling
                        )

                    [1] => stdClass Object
                        (
                            [name] => Ron Weasley
                            [orignal_name] => Rupert Grint
                        )

                )

        )

	

### Example 7: Create Option Group with Recursion on "childrens"

	$config = [
	    'optgroup' => [
	        'label' => 'title'
	    ],
	    'option' => [
	        'default' => [
	            'label' => 'Select Harry Potter Cast',
	            'value' => ''
	        ],
	        'label' => 'name',
	        'value' => 'orignal_name',
	    ]
	];
	$generator = new DropDownOptionsGenerator($config);
	$markput = $generator->generateMarkup($data);
	
	// Output
	//<option value=""  >Select Harry Potter Cast</option>
	//<optgroup label="Harry Potter and the Philosopher's Stone">
	//        <option value="Richard Harris"  >Professor Albus Dumbledore</option>
	//        <option value="Maggie Smith"  >Professor Minerva McGonagall</option>
	//</optgroup>
	//<optgroup label="Harry Potter and the Chamber of Secrets">
	//        <option value="Robbie Coltrane"  >Rubeus Hagrid</option>
	//        <option value="Daniel Radcliffe"  >Harry Potter</option>
	//</optgroup>
	//<optgroup label="Harry Potter and the Prisoner of Azkaban">
	//        <option value="Harry Melling"  >Dudley Dursley</option>
	//        <option value="Rupert Grint"  >Ron Weasley</option>
	//</optgroup>


### Example 7: Complete Configuration Reference

	$config = [
	    'optgroup' => [
	        'label' => '', // Required
	        'data_attributes' => [],
	        'condition' => [
	            'AND' => [],
	            'OR' => [],
	        ],
	    ],
	    'option' => [
	        'default' => [
	            'label' => 'Select',
	            'value' => ''
	        ],
	        'label' => 'column_name', // Required
	        'value' => 'column_name', // Required
	        'selected' => '',
	        'exclude' => [
	            'AND' => [],
	            'OR' => [],
	        ],
	        'data_attributes' => []
	    ]
	];