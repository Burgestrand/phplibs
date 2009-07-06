<?php
    require 'SplSubjectAdapter.php';

    // Example usage. Swedish only.
    // Konstruktorn är privat, så vi kan inte göra new Observable()
    $obj = SplSubjectAdapter::getInstance();

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
    $observers = array('show_class' => new SplObserverWrapper('show_class'),
                       'parent' => new SplObserverWrapper('parent'),
                       'child' => new SplObserverWrapper('child'),
                       'grandchild' => new SplObserverWrapper('grandchild'),
                       'sibling' => new SplObserverWrapper('sibling'));
    $obj->attach($observers['show_class'])->attach($observers['parent'])
        ->child->attach($observers['show_class'])->attach($observers['child'])
        ->grandchild->attach($observers['show_class'])->attach($observers['grandchild']);
    $obj->sibling->attach($observers['show_class'])->attach($observers['sibling']);
 
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
    
/* End of file SplSubjectAdapterExample.php */
/* Location: ./patterns/observer/SplSubjectAdapterExample.php */ 