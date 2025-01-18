import React, { useEffect, useState } from "react";
import { zodResolver } from "@hookform/resolvers/zod";
import { useForm, Controller } from "react-hook-form";
import { z } from "zod";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { Loader2 } from "lucide-react";
import { Check, ChevronsUpDown } from "lucide-react";
import { Textarea } from "@/components/ui/textarea";

import { cn } from "@/lib/utils";
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from "@/components/ui/command";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover";
import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import axios from "axios";
import { Button } from "@/components/ui/button";
import { useNavigate } from "react-router-dom";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { toast } from "sonner";

const formSchema = z.object({
  receipt_type_id: z.coerce.number().min(1, "Receipt Type field is required"),
  receipt_date: z.string().min(1, "receipt date field is required"),
  name: z.string().optional(),
  receipt_head: z.string().min(2, "receipt head field is required"),
  gotra: z.string().optional(),
  amount: z.coerce.number().min(1, "amount filed is required"),
  quantity: z.coerce.number().optional(),
  rate: z.coerce.number().optional(),
  email: z.string().optional(),
  special_date: z.string().optional(),
  payment_mode: z.string().optional(),
  mobile: z.coerce.string().optional(),
  pincode: z.coerce.string().optional(),
  address: z.string().optional(),
  narration: z.string().optional(),
  cheque_date: z.string().optional(),
  cheque_number: z.coerce.string().optional(),
  bank_details: z.string().optional(),
  remembrance: z.string().optional(),
  description: z.string().optional(),
  saree_draping_date: z.string().optional(),
  return_saree: z.coerce.number().min(0, "return saree field is required"),
  uparane_draping_date: z.string().optional(),
  return_uparane: z.coerce.number().min(0, "return Uparane field is required"),
  member_name: z.string().optional(),
  from_date: z.string().optional(),
  to_date: z.string().optional(),
  Mallakhamb: z.coerce.number().min(0, "mallakhamb field is required"),
  zanj: z.coerce.number().min(0, "zanj field is required"),
  dhol: z.coerce.number().min(0, "dhol field is required"),
  lezim: z.coerce.number().min(0, "lezim field is required"),
  hall: z.string().optional(),
  membership_no: z.string().optional(),
  timing: z.string().optional(),
  guruji: z.string().optional(),
  yajman: z.string().optional(),
  from_date: z.string().optional(),
  to_date: z.string().optional(),
  karma_number: z.string().optional(),
  day_10: z.coerce.number().min(0, "day 10 field is required"),
  day_11: z.coerce.number().min(0, "day 11 field is required"),
  day_12: z.coerce.number().min(0, "day 12 field is required"),
  day_13: z.coerce.number().min(0, "day 13 field is required"),
  date: z.string().optional(),
  pooja_type_id: z.coerce.number().optional(),
});

const Create = () => {
  const [isLoading, setIsLoading] = useState(false);
  const [openReceiptHead, setOpenReceiptHead] = useState(false);
  const [openReceiptType, setOpenReceiptType] = useState(false);
  const [openPoojaType, setOpenPoojaType] = useState(false);
  const [selectedReceiptHead, setSelectedReceiptHead] = useState("");
  const [selectedReceiptTypeId, setSelectedReceiptTypeId] = useState("");
  const [paymentMode, setPaymentMode] = useState("");
  const khatReceiptId = 1;
  const naralReceiptId = 2;
  const bhangarReceiptId = 3;
  const sareeReceiptId = 4;
  const uparaneReceiptId = 5;
  const vasturupeeReceiptId = 6;
  const campReceiptId = 7;
  const libraryReceiptId = 8;
  const hallReceiptId = 9;
  const studyRoomReceiptId = 10;
  const anteshteeReceiptId = 11;
  const poojaReceiptId = 12;

  const queryClient = useQueryClient();
  const user = JSON.parse(localStorage.getItem("user"));
  const token = user.token;
  const currentDate = new Date().toISOString().split("T")[0];
  const navigate = useNavigate();
  const defaultValues = {
    receipt_type_id: "",
    receipt_date: currentDate,
    name: "",
    gotra: "",
    amount: "",
    receipt_head: "",
    quantity: "",
    rate: "",
    email: "",
    mobile: "",
    address: "",
    narration: "",
    pincode: "",
    payment_mode: "",
    special_date: "",
    cheque_date: "",
    cheque_number: "",
    bank_details: "",
    remembrance: "",
    description: "",
    saree_draping_date: "",
    return_saree: "",
    uparane_draping_date: "",
    return_uparane: "",
    member_name: "",
    from_date: "",
    to_date: "",
    Mallakhamb: "",
    zanj: "",
    lezim: "",
    dhol: "",
    hall: "",
    membership_no: "",
    timing: "",
    guruji: "",
    yajman: "",
    from_date: "",
    to_date: "",
    day_10: "",
    day_11: "",
    day_12: "",
    day_13: "",
    pooja_type_id: "",
    date: "",
  };

  const {
    control,
    handleSubmit,
    formState: { errors },
    setError,
    setValue,
    watch,
  } = useForm({ resolver: zodResolver(formSchema), defaultValues });

  const {
    data: allPoojaTypesData,
    isLoading: isAllPoojaTypesDataLoading,
    isError: isAllPoojaTypesDataError,
  } = useQuery({
    queryKey: ["allPoojaTypes"], // This is the query key
    queryFn: async () => {
      try {
        const response = await axios.get(`/api/all_pooja_types`, {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        });
        return response.data?.data; // Return the fetched data
      } catch (error) {
        throw new Error(error.message);
      }
    },
    keepPreviousData: true, // Keep previous data until the new data is available
  });

  const {
    data: allReceiptTypesData,
    isLoading: isAllReceiptTypesDataLoading,
    isError: isAllReceiptTypesDataError,
  } = useQuery({
    queryKey: ["allReceiptTypes", selectedReceiptHead], // This is the query key
    queryFn: async () => {
      try {
        if (selectedReceiptHead) {
          setValue("receipt_type_id", "");
          handleReceiptTypeChange("");
        }
        const response = await axios.get(`/api/all_receipt_types`, {
          params: { receipt_head: selectedReceiptHead },
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        });
        return response.data?.data; // Return the fetched data
      } catch (error) {
        throw new Error(error.message);
      }
    },
    keepPreviousData: true, // Keep previous data until the new data is available
  });

  const {
    data: allReceiptHeadsData,
    isLoading: isAllReceiptHeadsDataLoading,
    isError: isAllReceiptHeadsDataError,
  } = useQuery({
    queryKey: ["allReceiptHeads"], // This is the query key
    queryFn: async () => {
      try {
        const response = await axios.get(`/api/all_receipt_heads`, {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        });
        return response.data?.data; // Return the fetched data
      } catch (error) {
        throw new Error(error.message);
      }
    },
    keepPreviousData: true, // Keep previous data until the new data is available
  });

  const storeMutation = useMutation({
    mutationFn: async (data) => {
      const response = await axios.post("/api/receipts", data, {
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`, // Include the Bearer token
        },
      });
      return response.data;
    },
    onSuccess: (data) => {
      queryClient.invalidateQueries("receipts");
      toast.success("Receipt Added Successfully");
      setIsLoading(false);
      navigate("/receipts");
    },
    onError: (error) => {
      setIsLoading(false);
      if (error.response && error.response.data.errors) {
        const serverStatus = error.response.data.status;
        const serverErrors = error.response.data.errors;
        if (serverStatus === false) {
          if (serverErrors.quantity) {
            setError("quantity", {
              type: "manual",
              message: serverErrors.quantity[0], // The error message from the server
            });
            // toast.error("The poo has already been taken.");
          }
          if (serverErrors.rate) {
            setError("rate", {
              type: "manual",
              message: serverErrors.rate[0], // The error message from the server
            });
            // toast.error("The poo has already been taken.");
          }
        } else {
          toast.error("Failed to add pooja type.");
        }
      } else {
        toast.error("Failed to add pooja type.");
      }
      console.log("got error ", error);
    },
  });
  const onSubmit = (data) => {
    setIsLoading(true);
    storeMutation.mutate(data);
  };

  const handleReceiptTypeChange = (value) => {
    setSelectedReceiptTypeId(value?.id);

    console.log(value?.minimum_amount, "ffff");
    console.log(value, "ffffhh");

    // setValue("amount", value?.minimum_amount);
    setValue("amount", value?.minimum_amount || null);
    console.log("swff", parseFloat(value?.minimum_amount));
  };

  const receiptAmount = watch(["quantity", "rate"]);
  useEffect(() => {
    const quantity = parseFloat(receiptAmount[0]) || 0;
    const rate = parseFloat(receiptAmount[1]) || 0;
    if (quantity && rate) {
      const totalAmount = (quantity * rate).toFixed(2); // Multiply instead of adding
      setValue("amount", totalAmount);
    }
  }, [receiptAmount, setValue]);

  return (
    <>
      <div className="p-5">
        {/* breadcrumb start */}
        <div className=" mb-7 text-sm">
          <div className="flex items-center space-x-2 text-gray-700">
            <span className="">
              <Button
                onClick={() => navigate("/receipts")}
                className="p-0 text-blue-700 text-sm font-light"
                variant="link"
              >
                Receipts
              </Button>
            </span>
            <span className="text-gray-400">/</span>
            <span className="dark:text-gray-300">Add</span>
          </div>
        </div>
        {/* breadcrumb ends */}

        {/* form style strat */}
        <form onSubmit={handleSubmit(onSubmit)}>
          <div className="px-5 pb-4 mb-5 dark:bg-background pt-1 w-full bg-white shadow-lg border  rounded-md">
            <div className="w-full py-3 flex justify-start items-center">
              <h2 className="text-lg  font-normal">Receipts Details</h2>
            </div>
            <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
              <div className="relative">
                <Label className="font-normal" htmlFor="receipt_no">
                  Receipt Number: <span className="text-red-500">*</span>
                </Label>
                <Controller
                  name="receipt_no"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="receipt_no"
                      className="mt-1 bg-gray-100"
                      type="text"
                      readOnly
                      // disabled="true"
                      placeholder=""
                    />
                  )}
                />
                {errors.receipt_no && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.receipt_no.message}
                  </p>
                )}
              </div>
              <div className="relative">
                <Label className="font-normal" htmlFor="receipt_date">
                  Receipt date:
                </Label>
                <Controller
                  name="receipt_date"
                  control={control}
                  render={({ field }) => (
                    <input
                      {...field}
                      id="receipt_date"
                      className="mt-1 text-sm w-full p-2 pr-3 rounded-md border border-1 bg-gray-100"
                      type="date"
                      readOnly
                      placeholder="Enter receipt date"
                    />
                  )}
                />
                {errors.receipt_date && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.receipt_date.message}
                  </p>
                )}
              </div>
              <div className="relative">
                <Label className="font-normal" htmlFor="receipt_head">
                  Receipt Head: <span className="text-red-500">*</span>
                </Label>
                {/* <Controller
                  name="receipt_head"
                  control={control}
                  render={({ field }) => (
                    <Select
                      value={field.value}
                      onValueChange={(value) => {
                        field.onChange(value);
                        setSelectedReceiptHead(value); // Set the selected receipt head
                      }}
                    >
                      <SelectTrigger className="mt-1">
                        <SelectValue placeholder="Select receipt head" />
                      </SelectTrigger>
                      <SelectContent className="pb-10">
                        <SelectGroup>
                          <SelectLabel>Select receipt head</SelectLabel>
                          {allReceiptHeadsData?.ReceiptHeads &&
                            Object.keys(allReceiptHeadsData?.ReceiptHeads).map(
                              (key) => (
                                <SelectItem key={key} value={key}>
                                  {allReceiptHeadsData.ReceiptHeads[key]}
                                </SelectItem>
                              )
                            )}
                        </SelectGroup>
                      </SelectContent>
                    </Select>
                  )}
                /> */}
                {/* <div className="w-full pt-1"> */}
                <Controller
                  name="receipt_head"
                  control={control}
                  render={({ field }) => (
                    <Popover
                      open={openReceiptHead}
                      onOpenChange={setOpenReceiptHead}
                    >
                      <PopoverTrigger asChild>
                        <Button
                          variant="outline"
                          role="combobox"
                          aria-expanded={openReceiptHead ? "true" : "false"} // This should depend on the popover state
                          className=" w-[325px] justify-between mt-1"
                          onClick={() => setOpenReceiptHead((prev) => !prev)} // Toggle popover on button click
                        >
                          {field.value
                            ? Object.keys(
                                allReceiptHeadsData?.ReceiptHeads
                              ).find((key) => key === field.value)
                            : "Select Receipt Head..."}
                          <ChevronsUpDown className="opacity-50" />
                        </Button>
                      </PopoverTrigger>
                      <PopoverContent className="w-[325px] p-0">
                        <Command>
                          <CommandInput
                            placeholder="Search receipt head..."
                            className="h-9"
                          />
                          <CommandList>
                            <CommandEmpty>No receipt head found.</CommandEmpty>
                            <CommandGroup>
                              {allReceiptHeadsData?.ReceiptHeads &&
                                Object.keys(
                                  allReceiptHeadsData?.ReceiptHeads
                                ).map((key) => (
                                  <CommandItem
                                    key={key}
                                    value={key}
                                    onSelect={(currentValue) => {
                                      setValue("receipt_head", key);
                                      setSelectedReceiptHead(
                                        currentValue === selectedReceiptHead
                                          ? ""
                                          : currentValue
                                      );
                                      setOpenReceiptHead(false);
                                      // Close popover after selection
                                    }}
                                  >
                                    {key}
                                    <Check
                                      className={cn(
                                        "ml-auto",
                                        key === field.value
                                          ? "opacity-100"
                                          : "opacity-0"
                                      )}
                                    />
                                  </CommandItem>
                                ))}
                            </CommandGroup>
                          </CommandList>
                        </Command>
                      </PopoverContent>
                    </Popover>
                  )}
                />
                {/* </div> */}
                {errors.receipt_head && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.receipt_head.message}
                  </p>
                )}
              </div>
            </div>
            <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
              <div className="relative">
                <Label className="font-normal" htmlFor="receipt_type_id">
                  Receipt Type: <span className="text-red-500">*</span>
                </Label>
                <Controller
                  name="receipt_type_id"
                  control={control}
                  render={({ field }) => (
                    <Popover
                      open={openReceiptType}
                      onOpenChange={setOpenReceiptType}
                    >
                      <PopoverTrigger asChild>
                        <Button
                          variant="outline"
                          role="combobox"
                          aria-expanded={openReceiptType ? "true" : "false"} // This should depend on the popover state
                          className=" w-[325px] justify-between mt-1"
                          onClick={() => setOpenReceiptType((prev) => !prev)} // Toggle popover on button click
                        >
                          {field.value
                            ? allReceiptTypesData?.ReceiptTypes &&
                              allReceiptTypesData?.ReceiptTypes.find(
                                (receiptType) => receiptType.id === field.value
                              )?.receipt_type
                            : "Select Receipt Type..."}
                          <ChevronsUpDown className="opacity-50" />
                        </Button>
                      </PopoverTrigger>
                      <PopoverContent className="w-[325px] p-0">
                        <Command>
                          <CommandInput
                            placeholder="Search receipt type..."
                            className="h-9"
                          />
                          <CommandList>
                            <CommandEmpty>No receipt type found.</CommandEmpty>
                            <CommandGroup>
                              {allReceiptTypesData?.ReceiptTypes &&
                                allReceiptTypesData?.ReceiptTypes.map(
                                  (receiptType) => (
                                    <CommandItem
                                      key={receiptType.id}
                                      value={receiptType.id}
                                      onSelect={(currentValue) => {
                                        setValue(
                                          "receipt_type_id",
                                          receiptType.id
                                        );
                                        // setSelectedReceiptTypeId(
                                        //   currentValue === selectedReceiptTypeId
                                        //     ? ""
                                        //     : currentValue
                                        // );
                                        handleReceiptTypeChange(receiptType);
                                        setOpenReceiptType(false);
                                        // Close popover after selection
                                      }}
                                    >
                                      {receiptType.receipt_type}
                                      <Check
                                        className={cn(
                                          "ml-auto",
                                          receiptType.id === field.value
                                            ? "opacity-100"
                                            : "opacity-0"
                                        )}
                                      />
                                    </CommandItem>
                                  )
                                )}
                            </CommandGroup>
                          </CommandList>
                        </Command>
                      </PopoverContent>
                    </Popover>
                  )}
                />
                {errors.receipt_type_id && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.receipt_type_id.message}
                  </p>
                )}
              </div>
              <div className="relative md:col-span-2">
                <Label className="font-normal" htmlFor="name">
                  Name:
                </Label>
                <Controller
                  name="name"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="name"
                      className="mt-1"
                      type="text"
                      placeholder="Enter name"
                    />
                  )}
                />
                {errors.name && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.name.message}
                  </p>
                )}
              </div>
            </div>

            <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
              <div className="relative ">
                <Label className="font-normal" htmlFor="gotra">
                  Gotra:
                </Label>
                <Controller
                  name="gotra"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="gotra"
                      className="mt-1"
                      type="text"
                      placeholder="Enter gotra"
                    />
                  )}
                />
                {errors.gotra && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.gotra.message}
                  </p>
                )}
              </div>
              <div className="relative ">
                <Label className="font-normal" htmlFor="email">
                  Email:
                </Label>
                <Controller
                  name="email"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="email"
                      className="mt-1"
                      type="email"
                      placeholder="Enter email"
                    />
                  )}
                />
                {errors.email && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.email.message}
                  </p>
                )}
              </div>
              <div className="relative ">
                <Label className="font-normal" htmlFor="mobile">
                  Mobile:
                </Label>
                <Controller
                  name="mobile"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="mobile"
                      className="mt-1"
                      type="number"
                      placeholder="Enter mobile"
                    />
                  )}
                />
                {errors.mobile && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.mobile.message}
                  </p>
                )}
              </div>
            </div>
            <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-1 gap-7 md:gap-4">
              <div className="relative ">
                <Label className="font-normal" htmlFor="address">
                  Address:
                </Label>
                <Controller
                  name="address"
                  control={control}
                  render={({ field }) => (
                    <Textarea
                      placeholder="Enter the address..."
                      className="resize-none mt-1 "
                      {...field}
                    />
                  )}
                />
                {errors.mobile && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.mobile.message}
                  </p>
                )}
              </div>
            </div>
            <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
              <div className="relative md:col-span-2">
                <Label className="font-normal" htmlFor="narration">
                  Narration:
                </Label>
                <Controller
                  name="narration"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="narration"
                      className="mt-1"
                      type="text"
                      placeholder="Enter narration"
                    />
                  )}
                />
                {errors.narration && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.narration.message}
                  </p>
                )}
              </div>
              <div className="relative">
                <Label className="font-normal" htmlFor="pincode">
                  Pincode:
                </Label>
                <Controller
                  name="pincode"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="pincode"
                      className="mt-1"
                      type="number"
                      placeholder="Enter pincode"
                    />
                  )}
                />
                {errors.pincode && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.pincode.message}
                  </p>
                )}
              </div>
            </div>

            <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
              <div className="relative md:col-span-2">
                <Label className="font-normal" htmlFor="remembrance">
                  Remembrance:
                </Label>
                <Controller
                  name="remembrance"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="remembrance"
                      className="mt-1"
                      type="text"
                      placeholder="Enter remembrance"
                    />
                  )}
                />
                {errors.remembrance && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.remembrance.message}
                  </p>
                )}
              </div>
              <div className="relative">
                <Label className="font-normal" htmlFor="special_date">
                  Special date:
                </Label>
                <Controller
                  name="special_date"
                  control={control}
                  render={({ field }) => (
                    <input
                      {...field}
                      id="special_date"
                      className="mt-1 text-sm w-full p-2 pr-3 rounded-md border border-1"
                      type="date"
                      placeholder="Enter special date"
                    />
                  )}
                />
                {errors.special_date && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.special_date.message}
                  </p>
                )}
              </div>
            </div>

            {(selectedReceiptTypeId === bhangarReceiptId ||
              selectedReceiptTypeId === vasturupeeReceiptId) && (
              <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
                <div className="relative">
                  <Label className="font-normal" htmlFor="description">
                    description:
                  </Label>
                  <Controller
                    name="description"
                    control={control}
                    render={({ field }) => (
                      <Select
                        value={field.value}
                        onValueChange={(value) => {
                          field.onChange(value);
                        }}
                      >
                        <SelectTrigger className="mt-1">
                          <SelectValue placeholder="Select description" />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectGroup>
                            <SelectLabel>Select description</SelectLabel>
                            {selectedReceiptTypeId === bhangarReceiptId && (
                              <>
                                <SelectItem value="रद्दी पेपर वा पुस्तके">
                                  रद्दी पेपर वा पुस्तके
                                </SelectItem>
                                <SelectItem value="इतर समान">
                                  इतर समान
                                </SelectItem>
                              </>
                            )}
                            {selectedReceiptTypeId === vasturupeeReceiptId && (
                              <>
                                <SelectItem value="पूजा साहित्य">
                                  पूजा साहित्य
                                </SelectItem>
                                <SelectItem value="सोने वा चांदी वस्तू">
                                  सोने वा चांदी वस्तू
                                </SelectItem>
                                <SelectItem value="उपकरणे वा इतर">
                                  उपकरणे वा इतर
                                </SelectItem>
                                <SelectItem value="देवी साडी वा उपरणे">
                                  देवी साडी वा उपरणे
                                </SelectItem>
                              </>
                            )}
                          </SelectGroup>
                        </SelectContent>
                      </Select>
                    )}
                  />
                  {errors.description && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.description.message}
                    </p>
                  )}
                </div>
              </div>
            )}

            {paymentMode === "Bank" && (
              <>
                <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
                  <div className="relative ">
                    <Label className="font-normal" htmlFor="bank_details">
                      Bank Details:
                    </Label>
                    <Controller
                      name="bank_details"
                      control={control}
                      render={({ field }) => (
                        <Textarea
                          placeholder="Enter bank details..."
                          className="resize-none mt-1 "
                          {...field}
                        />
                      )}
                    />
                    {errors.bank_details && (
                      <p className="absolute text-red-500 text-sm mt-1 left-0">
                        {errors.bank_details.message}
                      </p>
                    )}
                  </div>
                  <div className="relative ">
                    <Label className="font-normal" htmlFor="cheque_number">
                      Cheque Number:
                    </Label>
                    <Controller
                      name="cheque_number"
                      control={control}
                      render={({ field }) => (
                        <Input
                          {...field}
                          id="cheque_number"
                          className="mt-1"
                          type="text"
                          placeholder="Enter cheque number"
                        />
                      )}
                    />
                    {errors.cheque_number && (
                      <p className="absolute text-red-500 text-sm mt-1 left-0">
                        {errors.cheque_number.message}
                      </p>
                    )}
                  </div>
                  <div className="relative">
                    <Label className="font-normal" htmlFor="cheque_date">
                      Cheque date:
                    </Label>
                    <Controller
                      name="cheque_date"
                      control={control}
                      render={({ field }) => (
                        <input
                          {...field}
                          id="cheque_date"
                          className="mt-1 text-sm w-full p-2 pr-3 rounded-md border border-1"
                          type="date"
                          placeholder="Enter cheque date"
                        />
                      )}
                    />
                    {errors.cheque_date && (
                      <p className="absolute text-red-500 text-sm mt-1 left-0">
                        {errors.cheque_date.message}
                      </p>
                    )}
                  </div>
                </div>
                <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-1 gap-7 md:gap-4">
                  <div className="relative "></div>
                </div>
              </>
            )}

            {console.log(selectedReceiptTypeId, typeof selectedReceiptTypeId)}
            {/* {selectedReceiptTypeId === khatReceiptId && ( */}
            {(selectedReceiptTypeId === khatReceiptId ||
              selectedReceiptTypeId === naralReceiptId) && (
              <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
                <div className="relative">
                  <Label className="font-normal" htmlFor="quantity">
                    Quantity: <span className="text-red-500">*</span>
                  </Label>
                  <Controller
                    name="quantity"
                    control={control}
                    render={({ field }) => (
                      <Input
                        {...field}
                        id="quantity"
                        className="mt-1"
                        type="number"
                        placeholder="Enter quantity"
                      />
                    )}
                  />
                  {errors.quantity && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.quantity.message}
                    </p>
                  )}
                </div>
                <div className="relative ">
                  <Label className="font-normal" htmlFor="rate">
                    Rate: <span className="text-red-500">*</span>
                  </Label>
                  <Controller
                    name="rate"
                    control={control}
                    render={({ field }) => (
                      <Input
                        {...field}
                        id="rate"
                        className="mt-1"
                        type="text"
                        placeholder="Enter rate"
                      />
                    )}
                  />
                  {errors.rate && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.rate.message}
                    </p>
                  )}
                </div>
              </div>
            )}

            {selectedReceiptTypeId === sareeReceiptId && (
              <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
                <div className="relative">
                  <Label className="font-normal" htmlFor="saree_draping_date">
                    Saree Draping date:
                  </Label>
                  <Controller
                    name="saree_draping_date"
                    control={control}
                    render={({ field }) => (
                      <input
                        {...field}
                        id="saree_draping_date"
                        className="mt-1 text-sm w-full p-2 pr-3 rounded-md border border-1"
                        type="date"
                        placeholder="Enter date"
                      />
                    )}
                  />
                  {errors.saree_draping_date && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.saree_draping_date.message}
                    </p>
                  )}
                </div>
                <div className="relative flex gap-2 md:pt-10 md:pl-2 ">
                  <Controller
                    name="return_saree"
                    control={control}
                    render={({ field }) => (
                      <input
                        id="return_saree"
                        {...field}
                        type="checkbox"
                        className="peer h-4 w-4 shrink-0 rounded-sm border border-primary ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground"
                      />
                    )}
                  />
                  <Label className="font-normal" htmlFor="return_saree">
                    Return Saree
                  </Label>
                  {errors.return_saree && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.return_saree.message}
                    </p>
                  )}
                </div>
              </div>
            )}

            {selectedReceiptTypeId === uparaneReceiptId && (
              <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
                <div className="relative">
                  <Label className="font-normal" htmlFor="uparane_draping_date">
                    Uparane Draping date:
                  </Label>
                  <Controller
                    name="uparane_draping_date"
                    control={control}
                    render={({ field }) => (
                      <input
                        {...field}
                        id="uparane_draping_date"
                        className="mt-1 text-sm w-full p-2 pr-3 rounded-md border border-1"
                        type="date"
                        placeholder="Enter date"
                      />
                    )}
                  />
                  {errors.uparane_draping_date && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.uparane_draping_date.message}
                    </p>
                  )}
                </div>
                <div className="relative flex gap-2 md:pt-10 md:pl-2 ">
                  <Controller
                    name="return_uparane"
                    control={control}
                    render={({ field }) => (
                      <input
                        id="return_uparane"
                        {...field}
                        type="checkbox"
                        className="peer h-4 w-4 shrink-0 rounded-sm border border-primary ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground"
                      />
                    )}
                  />
                  <Label className="font-normal" htmlFor="return_uparane">
                    Return Uparane
                  </Label>
                  {errors.return_uparane && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.return_uparane.message}
                    </p>
                  )}
                </div>
              </div>
            )}

            {selectedReceiptTypeId === campReceiptId && (
              <div className="w-full  grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
                <div className="relative ">
                  <Label className="font-normal" htmlFor="member_name">
                    Member Name:
                  </Label>
                  <Controller
                    name="member_name"
                    control={control}
                    render={({ field }) => (
                      <Input
                        {...field}
                        id="member_name"
                        className="mt-1"
                        type="text"
                        placeholder="Enter name"
                      />
                    )}
                  />
                  {errors.member_name && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.member_name.message}
                    </p>
                  )}
                </div>
                <div className="relative">
                  <Label className="font-normal" htmlFor="from_date">
                    From date:
                  </Label>
                  <Controller
                    name="from_date"
                    control={control}
                    render={({ field }) => (
                      <input
                        {...field}
                        id="from_date"
                        className="mt-1 text-sm w-full p-2 pr-3 rounded-md border border-1"
                        type="date"
                        placeholder="Enter from date"
                      />
                    )}
                  />
                  {errors.from_date && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.from_date.message}
                    </p>
                  )}
                </div>
                <div className="relative">
                  <Label className="font-normal" htmlFor="to_date">
                    To date:
                  </Label>
                  <Controller
                    name="to_date"
                    control={control}
                    render={({ field }) => (
                      <input
                        {...field}
                        id="to_date"
                        className="mt-1 text-sm w-full p-2 pr-3 rounded-md border border-1"
                        type="date"
                        placeholder="Enter to date"
                      />
                    )}
                  />
                  {errors.to_date && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.to_date.message}
                    </p>
                  )}
                </div>
              </div>
            )}

            {selectedReceiptTypeId === campReceiptId && (
              <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-4 gap-7 md:gap-4">
                <div className="relative flex gap-2 mt-5 md:mt-0 md:pt-10 md:pl-2 ">
                  <Controller
                    name="Mallakhamb"
                    control={control}
                    render={({ field }) => (
                      <input
                        id="Mallakhamb"
                        {...field}
                        type="checkbox"
                        className="peer h-4 w-4 shrink-0 rounded-sm border border-primary ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground"
                      />
                    )}
                  />
                  <Label className="font-normal" htmlFor="Mallakhamb">
                    Mallakhamb
                  </Label>
                  {errors.Mallakhamb && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.Mallakhamb.message}
                    </p>
                  )}
                </div>
                <div className="relative flex gap-2 md:pt-10 md:pl-2 ">
                  <Controller
                    name="zanj"
                    control={control}
                    render={({ field }) => (
                      <input
                        id="zanj"
                        {...field}
                        type="checkbox"
                        className="peer h-4 w-4 shrink-0 rounded-sm border border-primary ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground"
                      />
                    )}
                  />
                  <Label className="font-normal" htmlFor="zanj">
                    Zanj
                  </Label>
                  {errors.zanj && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.zanj.message}
                    </p>
                  )}
                </div>
                <div className="relative flex gap-2 md:pt-10 md:pl-2 ">
                  <Controller
                    name="dhol"
                    control={control}
                    render={({ field }) => (
                      <input
                        id="dhol"
                        {...field}
                        type="checkbox"
                        className="peer h-4 w-4 shrink-0 rounded-sm border border-primary ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground"
                      />
                    )}
                  />
                  <Label className="font-normal" htmlFor="dhol">
                    Dhol
                  </Label>
                  {errors.dhol && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.dhol.message}
                    </p>
                  )}
                </div>
                <div className="relative flex gap-2 md:pt-10 md:pl-2 ">
                  <Controller
                    name="lezim"
                    control={control}
                    render={({ field }) => (
                      <input
                        id="lezim"
                        {...field}
                        type="checkbox"
                        className="peer h-4 w-4 shrink-0 rounded-sm border border-primary ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground"
                      />
                    )}
                  />
                  <Label className="font-normal" htmlFor="lezim">
                    lezim
                  </Label>
                  {errors.lezim && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.lezim.message}
                    </p>
                  )}
                </div>
              </div>
            )}

            {selectedReceiptTypeId === hallReceiptId && (
              <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-4 gap-7 md:gap-4">
                <div className="relative ">
                  <Label className="font-normal" htmlFor="hall">
                    Hall:
                  </Label>
                  <Controller
                    name="hall"
                    control={control}
                    render={({ field }) => (
                      <Input
                        {...field}
                        id="hall"
                        className="mt-1"
                        type="text"
                        placeholder="Enter hall name"
                      />
                    )}
                  />
                  {errors.hall && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.hall.message}
                    </p>
                  )}
                </div>
              </div>
            )}

            {(selectedReceiptTypeId === libraryReceiptId ||
              selectedReceiptTypeId === studyRoomReceiptId) && (
              <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
                <div className="relative ">
                  <Label className="font-normal" htmlFor="membership_no">
                    Membership Number:
                  </Label>
                  <Controller
                    name="membership_no"
                    control={control}
                    render={({ field }) => (
                      <Input
                        {...field}
                        id="membership_no"
                        className="mt-1"
                        type="text"
                        placeholder="Enter membership no."
                      />
                    )}
                  />
                  {errors.membership_no && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.membership_no.message}
                    </p>
                  )}
                </div>
                <div className="relative">
                  <Label className="font-normal" htmlFor="from_date">
                    From date:
                  </Label>
                  <Controller
                    name="from_date"
                    control={control}
                    render={({ field }) => (
                      <input
                        {...field}
                        id="from_date"
                        className="mt-1 text-sm w-full p-2 pr-3 rounded-md border border-1"
                        type="date"
                        placeholder="Enter from date"
                      />
                    )}
                  />
                  {errors.from_date && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.from_date.message}
                    </p>
                  )}
                </div>
                <div className="relative">
                  <Label className="font-normal" htmlFor="to_date">
                    To date:
                  </Label>
                  <Controller
                    name="to_date"
                    control={control}
                    render={({ field }) => (
                      <input
                        {...field}
                        id="to_date"
                        className="mt-1 text-sm w-full p-2 pr-3 rounded-md border border-1"
                        type="date"
                        placeholder="Enter to date"
                      />
                    )}
                  />
                  {errors.to_date && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.to_date.message}
                    </p>
                  )}
                </div>
              </div>
            )}

            {selectedReceiptTypeId === studyRoomReceiptId && (
              <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
                <div className="relative ">
                  <Label className="font-normal" htmlFor="timing">
                    Timing:
                  </Label>
                  <Controller
                    name="timing"
                    control={control}
                    render={({ field }) => (
                      <Input
                        {...field}
                        id="timing"
                        className="mt-1"
                        type="text"
                        placeholder="Enter timing"
                      />
                    )}
                  />
                  {errors.timing && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.timing.message}
                    </p>
                  )}
                </div>
              </div>
            )}

            {selectedReceiptTypeId === anteshteeReceiptId && (
              <div>
                <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
                  <div className="relative ">
                    <Label className="font-normal" htmlFor="guruji">
                      Guruji Name:
                    </Label>
                    <Controller
                      name="guruji"
                      control={control}
                      render={({ field }) => (
                        <Input
                          {...field}
                          id="guruji"
                          className="mt-1"
                          type="text"
                          placeholder="Enter name"
                        />
                      )}
                    />
                    {errors.guruji && (
                      <p className="absolute text-red-500 text-sm mt-1 left-0">
                        {errors.guruji.message}
                      </p>
                    )}
                  </div>
                  <div className="relative ">
                    <Label className="font-normal" htmlFor="yajman">
                      Yajman Name:
                    </Label>
                    <Controller
                      name="yajman"
                      control={control}
                      render={({ field }) => (
                        <Input
                          {...field}
                          id="yajman"
                          className="mt-1"
                          type="text"
                          placeholder="Enter name"
                        />
                      )}
                    />
                    {errors.yajman && (
                      <p className="absolute text-red-500 text-sm mt-1 left-0">
                        {errors.yajman.message}
                      </p>
                    )}
                  </div>
                  <div className="relative ">
                    <Label className="font-normal" htmlFor="karma_number">
                      Karma No:
                    </Label>
                    <Controller
                      name="karma_number"
                      control={control}
                      render={({ field }) => (
                        <Input
                          {...field}
                          id="karma_number"
                          className="mt-1"
                          type="text"
                          placeholder="Enter karma no"
                        />
                      )}
                    />
                    {errors.karma_number && (
                      <p className="absolute text-red-500 text-sm mt-1 left-0">
                        {errors.karma_number.message}
                      </p>
                    )}
                  </div>
                </div>

                <div className="w-full grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
                  <div className="relative">
                    <Label className="font-normal" htmlFor="from_date">
                      From date:
                    </Label>
                    <Controller
                      name="from_date"
                      control={control}
                      render={({ field }) => (
                        <input
                          {...field}
                          id="from_date"
                          className="mt-1 text-sm w-full p-2 pr-3 rounded-md border border-1"
                          type="date"
                          placeholder="Enter from date"
                        />
                      )}
                    />
                    {errors.from_date && (
                      <p className="absolute text-red-500 text-sm mt-1 left-0">
                        {errors.from_date.message}
                      </p>
                    )}
                  </div>
                  <div className="relative">
                    <Label className="font-normal" htmlFor="to_date">
                      To date:
                    </Label>
                    <Controller
                      name="to_date"
                      control={control}
                      render={({ field }) => (
                        <input
                          {...field}
                          id="to_date"
                          className="mt-1 text-sm w-full p-2 pr-3 rounded-md border border-1"
                          type="date"
                          placeholder="Enter to date"
                        />
                      )}
                    />
                    {errors.to_date && (
                      <p className="absolute text-red-500 text-sm mt-1 left-0">
                        {errors.to_date.message}
                      </p>
                    )}
                  </div>
                </div>
                <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-4 gap-7 md:gap-4">
                  <div className="relative flex gap-2 mt-5 md:mt-0 md:pt-10 md:pl-2 ">
                    <Controller
                      name="day_10"
                      control={control}
                      render={({ field }) => (
                        <input
                          id="day_10"
                          {...field}
                          type="checkbox"
                          className="peer h-4 w-4 shrink-0 rounded-sm border border-primary ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground"
                        />
                      )}
                    />
                    <Label className="font-normal" htmlFor="day_10">
                      Day 10
                    </Label>
                    {errors.day_10 && (
                      <p className="absolute text-red-500 text-sm mt-1 left-0">
                        {errors.day_10.message}
                      </p>
                    )}
                  </div>
                  <div className="relative flex gap-2 md:pt-10 md:pl-2 ">
                    <Controller
                      name="day_11"
                      control={control}
                      render={({ field }) => (
                        <input
                          id="day_11"
                          {...field}
                          type="checkbox"
                          className="peer h-4 w-4 shrink-0 rounded-sm border border-primary ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground"
                        />
                      )}
                    />
                    <Label className="font-normal" htmlFor="day_11">
                      Day 11
                    </Label>
                    {errors.day_11 && (
                      <p className="absolute text-red-500 text-sm mt-1 left-0">
                        {errors.day_11.message}
                      </p>
                    )}
                  </div>
                  <div className="relative flex gap-2 md:pt-10 md:pl-2 ">
                    <Controller
                      name="day_12"
                      control={control}
                      render={({ field }) => (
                        <input
                          id="day_12"
                          {...field}
                          type="checkbox"
                          className="peer h-4 w-4 shrink-0 rounded-sm border border-primary ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground"
                        />
                      )}
                    />
                    <Label className="font-normal" htmlFor="day_12">
                      Day 12
                    </Label>
                    {errors.day_12 && (
                      <p className="absolute text-red-500 text-sm mt-1 left-0">
                        {errors.day_12.message}
                      </p>
                    )}
                  </div>
                  <div className="relative flex gap-2 md:pt-10 md:pl-2 ">
                    <Controller
                      name="day_13"
                      control={control}
                      render={({ field }) => (
                        <input
                          id="day_13"
                          {...field}
                          type="checkbox"
                          className="peer h-4 w-4 shrink-0 rounded-sm border border-primary ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground"
                        />
                      )}
                    />
                    <Label className="font-normal" htmlFor="day_13">
                      Day 13
                    </Label>
                    {errors.day_13 && (
                      <p className="absolute text-red-500 text-sm mt-1 left-0">
                        {errors.day_13.message}
                      </p>
                    )}
                  </div>
                </div>
              </div>
            )}

            {selectedReceiptTypeId === poojaReceiptId && (
              <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
                <div className="relative">
                  <Label className="font-normal" htmlFor="pooja_type_id">
                    Pooja Type:
                  </Label>
                  <Controller
                    name="pooja_type_id"
                    control={control}
                    render={({ field }) => (
                      <Popover
                        open={openPoojaType}
                        onOpenChange={setOpenPoojaType}
                      >
                        <PopoverTrigger asChild>
                          <Button
                            variant="outline"
                            role="combobox"
                            aria-expanded={openPoojaType ? "true" : "false"} // This should depend on the popover state
                            className=" w-[325px] justify-between mt-1"
                            onClick={() => setOpenPoojaType((prev) => !prev)} // Toggle popover on button click
                          >
                            {field.value
                              ? allPoojaTypesData?.PoojaTypes &&
                                allPoojaTypesData?.PoojaTypes.find(
                                  (poojaType) => poojaType.id === field.value
                                )?.pooja_type
                              : "Select Pooja Type..."}
                            <ChevronsUpDown className="opacity-50" />
                          </Button>
                        </PopoverTrigger>
                        <PopoverContent className="w-[325px] p-0">
                          <Command>
                            <CommandInput
                              placeholder="Search pooja type..."
                              className="h-9"
                            />
                            <CommandList>
                              <CommandEmpty>No pooja type found.</CommandEmpty>
                              <CommandGroup>
                                {allPoojaTypesData?.PoojaTypes &&
                                  allPoojaTypesData?.PoojaTypes.map(
                                    (poojaType) => (
                                      <CommandItem
                                        key={poojaType.id}
                                        value={poojaType.id}
                                        onSelect={(currentValue) => {
                                          setValue(
                                            "pooja_type_id",
                                            poojaType.id
                                          );

                                          setOpenPoojaType(false);
                                          // Close popover after selection
                                        }}
                                      >
                                        {poojaType.pooja_type}
                                        <Check
                                          className={cn(
                                            "ml-auto",
                                            poojaType.id === field.value
                                              ? "opacity-100"
                                              : "opacity-0"
                                          )}
                                        />
                                      </CommandItem>
                                    )
                                  )}
                              </CommandGroup>
                            </CommandList>
                          </Command>
                        </PopoverContent>
                      </Popover>
                    )}
                  />
                  {errors.pooja_type_id && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.pooja_type_id.message}
                    </p>
                  )}
                </div>
                <div className="relative">
                  <Label className="font-normal" htmlFor="date">
                    date:
                  </Label>
                  <Controller
                    name="date"
                    control={control}
                    render={({ field }) => (
                      <input
                        {...field}
                        id="date"
                        className="mt-1 text-sm w-full p-2 pr-3 rounded-md border border-1"
                        type="date"
                        placeholder="Enter to date"
                      />
                    )}
                  />
                  {errors.date && (
                    <p className="absolute text-red-500 text-sm mt-1 left-0">
                      {errors.date.message}
                    </p>
                  )}
                </div>
              </div>
            )}

            <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
              <div className="relative">
                <Label className="font-normal" htmlFor="payment_mode">
                  Payment Mode:
                </Label>
                <Controller
                  name="payment_mode"
                  control={control}
                  render={({ field }) => (
                    <Select
                      value={field.value}
                      onValueChange={(value) => {
                        field.onChange(value);
                        setPaymentMode(value);
                      }}
                    >
                      <SelectTrigger className="mt-1">
                        <SelectValue placeholder="Select payent mode" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectGroup>
                          <SelectLabel>Select payment mode</SelectLabel>
                          <SelectItem value="Cash">Cash</SelectItem>
                          <SelectItem value="Bank">Bank</SelectItem>
                          <SelectItem value="Card">Card</SelectItem>
                        </SelectGroup>
                      </SelectContent>
                    </Select>
                  )}
                />
                {errors.payment_mode && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.payment_mode.message}
                  </p>
                )}
              </div>

              <div className="relative">
                <Label className="font-normal" htmlFor="amount">
                  Amount: <span className="text-red-500">*</span>
                </Label>
                <Controller
                  name="amount"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="amount"
                      className="mt-1"
                      type="number"
                      placeholder="Enter amount"
                    />
                  )}
                />
                {errors.amount && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.amount.message}
                  </p>
                )}
              </div>
            </div>

            {/* row ends */}
            <div className="w-full gap-4 mt-4 flex justify-end items-center">
              <Button
                type="button"
                className="dark:text-white shadow-xl bg-red-600 hover:bg-red-700"
                onClick={() => navigate("/receipts")}
              >
                Cancel
              </Button>

              <Button
                type="submit"
                disabled={isLoading}
                className=" dark:text-white  shadow-xl bg-green-600 hover:bg-green-700"
              >
                {isLoading ? (
                  <>
                    <Loader2 className="animate-spin mr-2" /> {/* Spinner */}
                    Submitting...
                  </>
                ) : (
                  "Submit"
                )}
              </Button>
            </div>
          </div>
        </form>
      </div>
    </>
  );
};

export default Create;
