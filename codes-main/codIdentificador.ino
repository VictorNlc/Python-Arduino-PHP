


#include <MCUFRIEND_kbv.h>


#define ROXO   0xA602FB
#define BRANCO 0xFFFF


MCUFRIEND_kbv tft;

void setup() {
  Serial.begin(9600);
  uint16_t ID = tft.readID();
  
  Serial.print("Identificador do display: ");
  Serial.println(ID, HEX);
  
  tft.begin(ID);
  tft.setRotation(1);
  tft.fillScreen(BRANCO);
  tft.setCursor(40, 100);
  tft.setTextColor(ROXO);
  tft.setTextSize(3);
  tft.print("Estou vivo!!!");
  tft.setCursor(100, 180);
  tft.print(ID, HEX);
}
void loop() {
}
