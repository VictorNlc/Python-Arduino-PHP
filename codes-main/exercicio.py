# x = int (input("escreva um valor"))
# y = int (input("escreva um segundo valor"))
# print(x*y)
#codigo para somar os dois valores


#print("escrava valores x e y para mostrar o maior")
#x =  int (input("escreva um valor:"))
#y = int (input("escreva um segundo valor:"))
#if x>y:
#    print ("o numero x é maior que o numero y")
#elif x == y:
#    print("os valores sao iguais")
#else:
#    print ("o valor de y é maior que x")
    #CODIGO PARA MOSTRAR QUAL É O MAIOR VALOR


n = int(input("Digite um número:"))
if n < 0:
    print("Número inválido. Digite apenas valores positivos")
if n == 0 or n == 1:
    print(f"{n} é um caso especial.")
else:
    if n == 2:
        print("2 é primo")
    elif n % 2 == 0:
        print(f"{n} não é primo, pois 2 é o único número par primo.")
    else:
        x = 3
        while(x < n):
            if n % x == 0:
                break
            x = x + 2
        if x == n:
            print(f"{n} é primo")
        else:
            print(f" {n} não é primo, pois é divisível por {x}")




            x= int(input("digite o numero da serie"))

            if x <= 0:
                print("numero invalido!")
            elif x == 1:
                print(1)
            elif x ==2:
                print(1)
            else: