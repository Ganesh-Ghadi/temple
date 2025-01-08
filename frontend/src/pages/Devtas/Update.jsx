import React, { useEffect, useState } from "react";
import { zodResolver } from "@hookform/resolvers/zod";
import { useForm, Controller } from "react-hook-form";
import { z } from "zod";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { Loader2 } from "lucide-react";

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
import { useNavigate, useParams } from "react-router-dom";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { toast } from "sonner";

const formSchema = z.object({
  devta_name: z.string().min(2, "Name must be at least 2 characters"),
});

const Update = () => {
  const [isLoading, setIsLoading] = useState(false);
  const queryClient = useQueryClient();
  const { id } = useParams();
  const user = JSON.parse(localStorage.getItem("user"));
  const token = user.token;
  const navigate = useNavigate();

  const defaultValues = {
    devta_name: "",
  };

  const {
    control,
    handleSubmit,
    formState: { errors },
    setValue,
  } = useForm({ resolver: zodResolver(formSchema), defaultValues });

  const {
    data: editDevta,
    isLoading: isEditDevtaDataLoading,
    isError: isEditDevtaDataError,
  } = useQuery({
    queryKey: ["editDevta", id], // This is the query key
    queryFn: async () => {
      try {
        const response = await axios.get(
          `http://127.0.0.1:8000/api/devtas/${id}`,
          {
            headers: {
              "Content-Type": "application/json",
              Authorization: `Bearer ${token}`,
            },
          }
        );
        return response.data?.data; // Return the fetched data
      } catch (error) {
        throw new Error(error.message);
      }
    },
    keepPreviousData: true, // Keep previous data until the new data is available
  });

  useEffect(() => {
    if (editDevta) {
      setValue("devta_name", editDevta.Devta?.devta_name);
    }
  }, [editDevta, setValue]);

  const updateMutation = useMutation({
    mutationFn: async (data) => {
      const response = await axios.put(
        `http://127.0.0.1:8000/api/devtas/${id}`,
        data,
        {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`, // Include the Bearer token
          },
        }
      );
      return response.data;
    },
    onSuccess: (data) => {
      queryClient.invalidateQueries("devtas");

      toast.success("Devta Updated Successfully");
      setIsLoading(false);
      navigate("/devtas");
    },
    onError: (error) => {
      setIsLoading(false);

      console.log("got error ", error);
    },
  });
  const onSubmit = (data) => {
    console.log("Clicked");
    setIsLoading(true);
    updateMutation.mutate(data);
  };

  return (
    <>
      <div className="p-5">
        {/* breadcrumb start */}
        <div className=" mb-11 text-sm">
          <div className="flex items-center space-x-2 text-gray-700">
            <span className="">
              {/* Users */}
              <Button
                onClick={() => navigate("/devtas")}
                className="p-0 text-blue-500"
                variant="link"
              >
                Devtas
              </Button>
            </span>
            <span className="text-gray-400">/</span>
            <span className="dark:text-gray-500">Edit</span>
          </div>
        </div>
        {/* breadcrumb ends */}

        {/* form style strat */}
        <div className="px-5 pb-7 pt-1 w-full dark:bg-gray-800 bg-white shadow-lg border  rounded-md">
          <div className="w-full py-3 flex justify-start items-center">
            <h2 className="text-lg  font-normal">Update Devta</h2>
          </div>
          {/* row starts */}
          <form onSubmit={handleSubmit(onSubmit)}>
            <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
              <div className="relative">
                <Label className="font-normal" htmlFor="devta_name">
                  Name: <span className="text-red-500">*</span>
                </Label>
                <Controller
                  name="devta_name"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="devta_name"
                      className="mt-1"
                      type="text"
                      placeholder="Enter name"
                    />
                  )}
                />
                {errors.devta_name && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.devta_name.message}
                  </p>
                )}
              </div>
            </div>

            {/* row ends */}
            <div className="w-full gap-4 mt-4 flex justify-end items-center">
              <Button
                type="button"
                className=" shadow-xl dark:text-white bg-red-500 hover:bg-red-600"
                onClick={() => navigate("/devtas")}
              >
                Cancle
              </Button>

              <Button
                type="submit"
                disabled={isLoading}
                className="shadow-xl dark:text-white bg-green-500 hover:bg-green-600"
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
          </form>
        </div>
      </div>
    </>
  );
};

export default Update;
