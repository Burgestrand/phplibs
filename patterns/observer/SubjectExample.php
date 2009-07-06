<?php
    require 'Subject.php';

    // Example usage. Swedish only.
    // Konstruktorn är privat, så vi kan inte göra new Observable()
    $obj = Subject::getInstance();

    // Skapa ett par funktioner som kan visa hur det fungerar
    function show_class()
    {
        $args = func_get_args();
        $obj = array_shift($args);
        printf("%s anropad.\n", ucfirst((string)$obj));
    }
    function parent()
    {
        printf("Hello from Parent!\n");
    }
    function child()
    {
        printf("Hello from Child!\n");
    }
    function grandchild()
    {
        printf("Hello from Grandchild!\n");
    }
    function sibling()
    {
        printf("Hello from Sibling!\n");
    }

    // Skriv upp funktionerna på listan utav funktioner som vill veta när något händer
    $obj->attach('show_class', 'parent')
        ->child->attach('show_class', 'child')
        ->grandchild->attach('show_class', 'grandchild');
    $obj->sibling->attach('show_class', 'sibling');
    /* Ovanstående är samma sak som:
     * $obj->attach('show_class', 'parent');
     * $obj->child->attach('show_class', 'child');
     * $obj->sibling->attach('show_class', 'sibling');
     * $obj->child->grandchild->attach('show_class', 'grandchild');
     */
 
    // Anropa funktionerna
    // Notera att ett “barn” till ett objekt bara skvallrar till sina föräldrar, inte sina syskon
    printf("[\$obj->notify('Mästerklassen')]\n");
    $obj->notify('Mästerklassen');
    printf("[\$obj->child->notify('Knatteklassen')]\n");
    $obj->child->notify('Knatteklassen');
    printf("[\$obj->child->grandchild->notify('Knatteknatteklassen')]\n");
    $obj->child->grandchild->notify('Knatteknatteklassen');
    printf("[\$obj->sibling->notify('Syskon')]\n");
    $obj->sibling->notify('Syskon');
    
/* End of file SubjectExample.php */
/* Location: ./patterns/observer/SubjectExample.php */ 