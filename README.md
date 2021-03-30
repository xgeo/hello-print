## Hello Print
---------------------------------------------------------------

#### Installation
*With docker-compose up --build you can install 
all dependencies and start all application*
- `docker-compose up --build`

---------------------------------------------------------------

#### Database

- (**PGAdmin**): http://localhost:16543/
- **username**: geovannylc@gmail.com
- **password**: @geo@geovanny@
- **database**: hello_print


---------------------------------------------------------------

#### Commands

- To produce a message to some topic: 
`
    php requester.php --topic="someTopicNameA" --message="some message" --randomize=true|false --broadcast_topic="someTopicNameB"
`
---------------------------------------------------------------
- Listen messages from some topic:
`
    php listener.php --topic="someTopicB" --message="append Some Random Message" --append="start|end"
`
---------------------------------------------------------------