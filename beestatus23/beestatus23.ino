//BEECONNECTED arduino - beehive remote monitoring system.
//copyright prandipadaro@gmail.com all right reserved!

// standard line packet
//String packet = data=123t19td0dh36hp5pb4bv0vr1.380r|s:1w117137s:2w119691s:3w82763s:4w102769s:5w360350 // change all string separator by caps string, eg: 


//LIBRARIES
#include <dht11.h>
#include <HX711.h>
#include <GSM.h>
#include <LowPower.h>
#include <avr/io.h>
#include <avr/interrupt.h>

//VARIABLES

// APN data
#define PINNUMBER "" // PIN Number
#define GPRS_APN       "mobile.vodafone.it" // replace your GPRS APN
#define GPRS_LOGIN     ""    // replace with your GPRS login
#define GPRS_PASSWORD  "" // replace with your GPRS password

// initialize the library instance

//gsm
GSMClient client;
GPRS gprs;
GSM gsmAccess; 
GSMScanner scannerNetworks;

//dht11
dht11 DHT;
#define DHT11_PIN A2
int chk;

// io & interrupt variables
boolean resetting = true;
volatile byte interrupter = 0;

// URL, path & port (for example: arduino.cc)
char server[] = "beestatus.altervista.org";
char path[] = "/controller/addParams0.2.php";
int port = 80; // port 80 is the default for HTTP

//sensors variables
#define TEMPERATURE A0
#define BRIGHTNESS A1
#define VOLTAGE A3
HX711 REFCELL(A4,A5);

int s0 = 8;
int s1 = 9;
int s2 = 10;
int s3 = 11;

int readMux(int channel){
  int controlPin[] = {s0, s1, s2, s3};

  int muxChannel[16][4]={
    {0,0,0,0}, //channel 0
    {1,0,0,0}, //channel 1
    {0,1,0,0}, //channel 2
    {1,1,0,0}, //channel 3
    {0,0,1,0}, //channel 4
    {1,0,1,0}, //channel 5
    {0,1,1,0}, //channel 6
    {1,1,1,0}, //channel 7
    {0,0,0,1}, //channel 8
    {1,0,0,1}, //channel 9
    {0,1,0,1}, //channel 10
    {1,1,0,1}, //channel 11
    {0,0,1,1}, //channel 12
    {1,0,1,1}, //channel 13
    {0,1,1,1}, //channel 14
    {1,1,1,1}  //channel 15
  };

  //loop through the 4 sig
  for(int i = 0; i < 4; i ++){
    digitalWrite(controlPin[i], muxChannel[channel][i]);
  }

  //read the value at the SIG pin
  //int val = analogRead(SIG_pin);
  long val = REFCELL.read_average(1);
  //return the value
  return val;
}

//SENDING DATA
char *atmosphere[] ={
    "X",//0 code
    "X",//1 temp
    "X",//2 hum
    "X",//3 press
    "X",//4 brig
    "X",//5 volt
    "X",//6 refcell
}; 

const String atmosphereCode[] = {
    "", //0 code
    "t",//1 temp
    "h",//2 hum
    "p",//3 press
    "b",//4 brig
    "v",//5 volt
    "r",//6 refcell
};

char dt[] ={
    4,
    6,
    8,
    10,
    12,
};

char sck[] ={
    5,
    7,
    9,
    11,
    13,
};

String weights = "|";
String Cells(){
    Serial.print(F("cells reading: "));
    int i;
    for (i = 0; i < 5; i ++) { //remember to use SIZEOF!!
        HX711 cell(dt[i],sck[i]);
        delay(10);
        cell.power_up();
        delay(10);
        long weight = cell.read_average(10);
        delay(10);
        cell.power_down();
        delay(10);
        weight = weight - 8388607;
        char scale[10];
        delay(10);
        dtostrf(weight,1,0,scale);
        weights.concat("s:");
        weights.concat(i+1);
        weights.concat("w");
        weights.concat(scale);
    };
    //Serial.println(weights);
    delay(10);
    return weights;
};

//FUNCTIONS

// Data sending constructor
String data;
String DataFormatting(){
    data = "data=";
    int i;
    int k;
    for (i = 0; i < 7 ; i ++) {
        if (atmosphere[i] != "X"){
            data.concat(atmosphereCode[i]);
            data.concat(atmosphere[i]);
            data.concat(atmosphereCode[i]);
            atmosphere[i] = "X";
        }    
    }
    data.concat(weights);
    //Serial.println(data);
    delay(10);
    return data;
}

//POWER SAVE
int PowerSaveMode(int amount){
    //1h = 3600sec
    //3600 / 8sec = 450
    delay(100);
    Serial.println(F("Power save mode"));
    delay(1000);
    client.stop();
    delay(100);
    gsmAccess.shutdown();// forse solo gsm
    delay(100);
    Serial.println(F("GSM shield down"));
    int i;
    boolean sleep = true;
    //for (i = 0; i < amount; i += 1){
    for (i = 0; i < amount; i += 1){
        LowPower.powerDown(SLEEP_8S, ADC_OFF, BOD_OFF);
    };
    Reset();
}

//CONNECTIONS!
String signalStrenght;
boolean Connection(){ //function that start a connection to the server
    Serial.println(F("CONNECTION ROUTINE"));
    
    boolean Connected = true;

    delay(100);

    delay(100);
   
    int i;

    for (i = 0; i < 10; i = i + 1){
        delay(100);
        Connected = false;
        Serial.print(F("Attempt to connect: "));
        Serial.flush() ;
        unsigned long myTimeout = 60000; // YOUR LIMIT IN MILLISECONDS
        unsigned long timeConnect = millis();
        
        while((millis() - timeConnect) < myTimeout)
        {
            if((gsmAccess.begin(PINNUMBER)==GSM_READY) &
              (gprs.attachGPRS(GPRS_APN, GPRS_LOGIN, GPRS_PASSWORD)==GPRS_READY)){
              Connected = true;
              return Connected;
              }
              
            else
            {
              Serial.println("Not connected");
              delay(1000);
            }
        }
        delay(100);
        

        

    }
    return Connected;
    
}

boolean Shipment(){
    delay(100);
    boolean Connected = false;
    delay(100);
    if (client.connect(server, port))
    {
        delay(100);
        Serial.println(F("Handling packet"));
        delay(100);
        // Make a HTTP request:
        client.print("GET ");
        client.print(path);
        client.print("?");
        client.print(data);
        client.println(" HTTP/1.1");
        client.print("Host: ");
        client.println(server);
        client.println("User-Agent: Arduino/1.0");
        client.println("Connection: close");
        client.println();
        Serial.println(F("Data send"));
        delay(100);
        resetting = false;
        delay(100);
        return true;
    }
    else{
        Serial.println(F("Client connection lost"));
        Reset();
    };
}

//INITIALIZE 
void setup(){    
    Serial.begin(1200);
    delay(10);
    Serial.println(F("START SETUP"));
    // initialize Timer1
    cli();          // disable global interrupts
    TCCR1A = 0;     // set entire TCCR1A register to 0
    TCCR1B = 0;     // same for TCCR1B
    // set compare match register to desired timer count:
    OCR1A = 15624;
    // turn on CTC mode:
    TCCR1B |= (1 << WGM12);
    // Set CS10 and CS12 bits for 1024 prescaler:
    TCCR1B |= (1 << CS10);
    TCCR1B |= (1 << CS12);
    // enable timer compare interrupt:
    TIMSK1 |= (1 << OCIE1A);
    // enable global interrupts:
    sei();
    delay(10);
    Serial.println(F("SETUP END"));    
}

//reset functions must be here i think
void(* reset) (void) = 0; //declare reset function @ address 0
boolean Reset(){
    delay(1000);
    Serial.println(F(" Time Expired - RESET!"));
    delay(1000);
    reset();  //call reset
}

//MAIN LOOP
void loop(){
    delay(2000);
    resetting = true;
    
    Serial.println(F("START-LOOP-START"));
    delay(10);
    /*
    //CELL REFERENCE
    REFCELL.power_up();
    delay(10);
    long refcell = REFCELL.read_average(10);
    delay(10);
    REFCELL.power_down();
    delay(10);
    refcell = refcell - 8388607;
    float cell0 = refcell * 0.000037;
    char c0[6];
    dtostrf(cell0,3,3,c0);
    atmosphere[6] = c0;
    delay(10);

    //DHT11
    delay(10);
    chk = DHT.read(DHT11_PIN);
    int outIgro;
    int outTemp;
    if (chk == DHTLIB_OK){
        delay(10);
        outIgro = (DHT.humidity);
        delay(10);
        outTemp = (DHT.temperature);
        delay(10);
    };
    delay(10);
    char oT[5];
    dtostrf(outTemp,1,0,oT);
    char oI[5];
    dtostrf(outIgro,1,0,oI);

    //TEMPERATURE
    int temperature = (((analogRead(TEMPERATURE) / 1024.0) * 5.0)- 0.5)*100;
    char iT[5] ;
    dtostrf(temperature, 1,0, iT);
    delay(10);

    //BRIGHTNESS
    int brightness = analogRead(BRIGHTNESS)/10;
    char br[5];
    delay(10);
    dtostrf(brightness, 1,0, br);
    delay(10);

    //VOLTMETER
    float voltage = analogRead(VOLTAGE); 
    
    //634:12.15=read:x
    //12.15*read/634
    voltage = (12.15*voltage)/634;
    char vT[4];
    delay(10);
    dtostrf(voltage,2,2,vT);
    delay(10);

    //PACKET COMPRESSOR
    atmosphere[0] = "123";
    atmosphere[1] = oT;
    atmosphere[2] = oI;
    atmosphere[3] = iT;
    atmosphere[4] = br;
    atmosphere[5] = vT;
    Cells();
    String packet = DataFormatting();
    Serial.println((packet));
    
    //DELIVERY PACKAGE
    delay(10);
    */
    
    boolean online = Connection();
    delay(10);
    if (online){
        boolean shipment = Shipment();
    };
    /*
    delay(10);

    //POWERSAVE!!
    int Amount = 105; // last used 205
    PowerSaveMode(Amount);
    Reset();
    //RESET OPERATIONS ?? maybe not

        Serial.println(F("END-END-END-END-END"));
       */
}
//INTERRUPTER
ISR(TIMER1_COMPA_vect)
{
    interrupter ++;
    if (interrupter>150 && resetting){
        Reset();
    }
}

